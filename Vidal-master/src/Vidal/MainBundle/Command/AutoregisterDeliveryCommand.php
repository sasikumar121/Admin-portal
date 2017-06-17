<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Vidal\MainBundle\Entity\DeliveryLog;
use Vidal\MainBundle\Entity\User;

class AutoregisterDeliveryCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:autoregister_delivery');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:autoregister_delivery started');

		$this->sendToAll($output);

		$output->writeln("+++ vidal:autoregister_delivery completed!");
	}

	private function sendToAll(OutputInterface $output)
	{
		$container = $this->getContainer();
		/** @var EntityManager $em */
		$em = $container->get('doctrine')->getManager();
		$templating = $container->get('templating');

		$deliveryId = 'Autoregister_12.06.2017';
        $testMode = false;

		# пользователи
        $users = $em->createQuery("
			SELECT u.username, u.id, DATE_FORMAT(u.created, '%Y-%m-%d_%H:%i:%s') as created,
			    u.firstName, u.lastName, u.surName, u.hash, u.password
			FROM VidalMainBundle:User u
			WHERE u.autoregister = TRUE AND u.emailConfirmed = FALSE
		")->getResult();

        if ($testMode) {
            $users = $em->createQuery("
                SELECT u.username, u.id, DATE_FORMAT(u.created, '%Y-%m-%d_%H:%i:%s') as created,
                    u.firstName, u.lastName, u.surName, u.hash, u.password
                FROM VidalMainBundle:User u
                WHERE u.username = '7binary@gmail.com'
            ")->getResult(); #  7binary@gmail.com  si-bu@yandex.ru
        }

        $total = count($users);
		$subject = 'VIDAL.ru – благодарим за регистрацию!';
		$pdo = $em->getConnection();
		$stmt = $pdo->prepare("SELECT COUNT(email) FROM delivery_log WHERE uniqueid = :uniqueid AND email = :email");

		# рассылка
		for ($i = 0; $i < $total; $i++) {
		    # проверка, что не рассылали еще
            $stmt->bindParam(':uniqueid', $deliveryId);
            $stmt->bindParam(':email', $users[$i]['username']);
            $stmt->execute();
            $count = intval($stmt->fetchColumn());

            if ($count != 0) {
                continue;
            }

            # шаблон письма
            $html =  $templating->render('VidalMainBundle:Email:autoregister_users.html.twig', array(
                'user' => $users[$i],
                'deliveryId' => $deliveryId
            ));

            # логирование и рассылка
			$this->log($deliveryId, $users[$i], $em);
			$this->send($users[$i]['username'], $users[$i]['firstName'], $html, $subject, $testMode);

			$count = $i + 1;
			$output->writeln("... $count / $total ({$users[$i]['username']})");
			sleep(1);
		}

		$output->writeln('=> Completed!');

	}

	private function log($deliveryId, $user, EntityManager $em)
	{
		# сохраняем лог, кому отправили
		$deliveryLog = new DeliveryLog();
		$deliveryLog->setEmail($user['username']);
		$deliveryLog->setUniqueid($deliveryId);
		$deliveryLog->setUserId($user['id']);
		$em->persist($deliveryLog);
		$em->flush($deliveryLog);
	}

	public function send($email, $to, $body, $subject, $testMode = false)
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

		if ($testMode) {
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