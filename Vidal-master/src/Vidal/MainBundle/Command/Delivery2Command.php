<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Process\Process;
use Vidal\MainBundle\Entity\Digest;

class Delivery2Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:delivery2')
            ->setDescription('Send digest')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Send digest to manager e-mails')
            ->addOption('stop', null, InputOption::VALUE_NONE, 'Stop sending digests')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Clean log app/logs/digest_sent.txt')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Send digest to every subscribed user')
            ->addOption('me', null, InputOption::VALUE_NONE, 'Send digest to 7binary@gmail.com')
            ->addOption('local', null, InputOption::VALUE_NONE, 'Send digest from 7binary@list.ru');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);
        ini_set('memory_limit', -1);

        # если ни одна опция не указана - выводим мануал
        if (!$input->getOption('test') && !$input->getOption('clean') && !$input->getOption('all') && !$input->getOption('me') && !$input->getOption('local') && !$input->getOption('stop')) {
            $output->writeln('=> Error: uncorrect syntax. READ BELOW');
            $output->writeln('$ php app/console evrika:delivery --test');
            $output->writeln('$ php app/console evrika:delivery --stop');
            $output->writeln('$ php app/console evrika:delivery --clean');
            $output->writeln('$ php app/console evrika:delivery --all');
            $output->writeln('$ php app/console evrika:delivery --me');
            $output->writeln('$ php app/console evrika:delivery --me --local');

            return false;
        }

        /** @var Container $container */
        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        /** @var Digest $digest */
        $digest = $em->getRepository('VidalMainBundle:Digest')->get();

        # --stop   остановка рассылки дайджеста
        if ($input->getOption('stop')) {
            $digest->setProgress(false);
            $em->flush();
            $this->fileRemove();
            $output->writeln('=> digest STOPPED');

            return true;
        }

        if ($input->getOption('clean')) {
            $em->createQuery('UPDATE VidalMainBundle:User u SET u.send2=0 WHERE u.send2=1')->execute();
            $digest->setProgress(false);
            $em->flush();
            $this->fileRemove();
            $output->writeln('=> users CLEANED');
            $output->writeln('=> digest STOPPED');
        }

        # рассылка нашим менеджерам
        if ($input->getOption('test')) {
            $raw = explode(';', $digest->getEmails());
            $emails = array();

            foreach ($raw as $email) {
                $emails[] = trim($email);
            }
            $output->writeln("=> Sending: in progress to managers: " . implode(', ', $emails));
            $this->sendTo($emails);
        }

        # отправить самому себе
        if ($input->getOption('me')) {
            $output->writeln("=> Sending: in progress to 7binary@bk.ru");
            $this->sendTo(array('7binary@bk.ru'), $input->getOption('local'));
        }

        # если статус рассылки не запущен или уже запущен с имеющимся файлом - прерываем
        if (!$digest->getProgress()) {
            $output->writeln('-- Digest progress is false @ database');
            return false;
        }

        if ($this->fileExists()) {
            $output->writeln('-- Digest already has lock file created');
            return false;
        }

        # рассылка всем подписанным врачам
        if ($input->getOption('all')) {
            $output->writeln("=> Sending: in progress to ALL subscribed users...");
            $digest->setProgress(true);
            $em->flush();
            $this->fileCreate();
            $this->sendToAll($output);
        }

        return true;
    }

    private function fileExists()
    {
        return file_exists(__DIR__ . DIRECTORY_SEPARATOR . '.delivery');
    }

    private function fileRemove()
    {
        @unlink(__DIR__ . DIRECTORY_SEPARATOR . '.delivery');
    }

    private function fileCreate()
    {
        $fp = @fopen(__DIR__ . DIRECTORY_SEPARATOR . '.delivery', "a+");
        @fclose($fp);
    }

    private function sendToAll(OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $templating = $container->get('templating');
        $digest = $em->getRepository('VidalMainBundle:Digest')->get();

        $step = 40;
        $sleep = 60;

        # пользователи
        $qb = $em->createQueryBuilder();
        $qb->select("u.username, u.id, DATE_FORMAT(u.created, '%Y-%m-%d_%H:%i:%s') as created, u.firstName")
            ->from('VidalMainBundle:User', 'u')
            ->where('u.send2 = 0')
            ->andWhere('u.digestSubscribed = 1')
            ->andWhere('(u.primarySpecialty IN (87) OR u.secondarySpecialty IN (87))')
            ->setMaxResults(500);

        $users = $qb->getQuery()->getResult();

        # всего рассылать
        $qb = $em->createQueryBuilder();
        $qb->select('COUNT(u.id)')
            ->from('VidalMainBundle:User', 'u')
            ->andWhere('u.digestSubscribed = 1')
            ->andWhere('(u.primarySpecialty IN (87) OR u.secondarySpecialty IN (87))')
            ->setMaxResults(500);

        $total = $qb->getQuery()->getSingleScalarResult();
        $digest->setTotal($total);
        $em->flush($digest);

        $subject = $digest->getSubject();
        $limit = $digest->getLimit();
        $template1 = $templating->render('VidalMainBundle:Digest:template1.html.twig', array('digest' => $digest));
        $sendQuery = $em->createQuery('SELECT COUNT(u.id) FROM VidalMainBundle:User u WHERE u.send2 = 1');
        $checkQuery = $em->createQuery('SELECT u.send2 FROM VidalMainBundle:User u WHERE u.send2 = 1 AND u.id = :id');
        $updateQuery = $em->createQuery('UPDATE VidalMainBundle:User u SET u.send2=1 WHERE u.id = :id');

        # рассылка
        for ($i = 0; $i < count($users); $i++) {
            $template2 = $templating->render('VidalMainBundle:Digest:template2.html.twig', array('user' => $users[$i], 'digest' => $digest));
            $template = $template1 . $template2;

            # проверяем еще раз, что пользователю не было отправлено
            if ($user = $checkQuery->setParameter('id', $users[$i]['id'])->getOneOrNullResult()) {
                continue;
            }

            $this->send($users[$i]['username'], $users[$i]['firstName'], $template, $subject);

            # обновляем пользователя
            $updateQuery->setParameter('id', $users[$i]['id'])->execute();

            if ($i && $i % $step == 0) {
                # проверка, можно ли продолжать рассылать
                $em->refresh($digest);
                if (false === $digest->getProgress()) {
                    break;
                }

                $send = $sendQuery->getSingleScalarResult();
                $digest->setTotalSend($send);
                $digest->setTotalLeft($total - $send);

                $em->flush($digest);

                $output->writeln("... sent $send / {$digest->getTotal()}");

                $em->getConnection()->close();
                sleep($sleep);
                $em->getConnection()->connect();
            }

            if ($limit && $i && $i % $limit == 0) {
                $em->getConnection()->close();
                sleep(1 * 60 * 60);
                $em->getConnection()->connect();
            }
        }

        $send = $sendQuery->getSingleScalarResult();
        $digest->setTotalSend($send);
        $digest->setTotalLeft($total - $send);
        $digest->setProgress(false);

        $em->flush($digest);
        $this->fileRemove();

        $output->writeln('=> Completed!');

    }

    /**
     * Рассылка по массиву почтовых адресов без логирования
     *
     * @param array $emails
     */
    private function sendTo(array $emails, $local = false)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $templating = $container->get('templating');
        $digest = $em->getRepository('VidalMainBundle:Digest')->get();

        $users = $em->createQuery("
			SELECT u.username, u.id, DATE_FORMAT(u.created, '%Y-%m-%d_%H:%i:%s') as created, u.firstName
			FROM VidalMainBundle:User u
			WHERE u.username IN (:emails)
		")->setParameter('emails', $emails)
            ->getResult();

        $subject = $digest->getSubject();
        $template1 = $templating->render('VidalMainBundle:Digest:template1.html.twig', array('digest' => $digest));

        foreach ($users as $user) {
            $template2 = $templating->render('VidalMainBundle:Digest:template2.html.twig', array('user' => $user, 'digest' => $digest));
            $template = $template1 . $template2;

            $this->send($user['username'], $user['firstName'], $template, $subject, $local);
        }
    }

    public function send($email, $to, $body, $subject, $local = false)
    {
        $mail = new \PHPMailer();

        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->From = 'maillist@vidal.ru';
        $mail->FromName = 'Портал «Vidal.ru»';
        $mail->Subject = $subject;
        $mail->Host = '127.0.0.1';
        $mail->Body = $body;
        $mail->addAddress($email, $to);
        $mail->addCustomHeader('Precedence', 'bulk');

        if ($local) {
            $mail->Host = 'smtp.yandex.ru';
            $mail->From = 'binacy@yandex.ru';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->SMTPAuth = true;
            $mail->Username = 'binacy@yandex.ru';
            $mail->Password = 'oijoijoij';
        }

        $result = $mail->send();
        $mail = null;

        return $result;
    }
}
