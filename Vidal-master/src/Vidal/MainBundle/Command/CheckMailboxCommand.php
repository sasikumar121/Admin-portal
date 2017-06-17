<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\MainBundle\Entity\User;

class CheckMailboxCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:check_mailbox');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $cwsDebug = new \Vidal\MainBundle\MailBounceHandler\Models\CwsDebug();
        //$cwsDebug->setDebugVerbose();
        //$cwsDebug->setEchoMode();
        $cwsMbh = new  \Vidal\MainBundle\MailBounceHandler\Handler($cwsDebug);
        $cwsMbh->setNeutralProcessMode(); // default

        $cwsMbh->setMailboxHost('mail.vidal.ru'); //
        $cwsMbh->setMailboxPort(143);
        $cwsMbh->setMailboxUsername('maillist@vidal.ru');
        $cwsMbh->setMailboxPassword('Te7R2XeX');
        $cwsMbh->setImapMailboxService(); // default
        $cwsMbh->setMailboxName('INBOX'); // default 'INBOX'
        $cwsMbh->setDeleteProcessMode(); // удалить после обработки

        if ($cwsMbh->openImapRemote() === false) {
            $error = $cwsMbh->getError();
            echo '!!! ERROR : $cwsMbh->openImapRemote() === false : ' . $error . PHP_EOL;
            exit;
        }

        $result = $cwsMbh->processMails();
        if (!$result instanceof \Vidal\MainBundle\MailBounceHandler\Models\Result) {
            $error = $cwsMbh->getError();
            echo '!!! ERROR : not instanceof \Vidal\MainBundle\MailBounceHandler\Models\Result : ' . $error . PHP_EOL;
            exit;
        }

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $selectQuery = $em->createQuery("
			SELECT u
			FROM VidalMainBundle:User u
			WHERE u.username = :email
		");

        foreach ($result->getMails() as $mail) {
            if (!$mail instanceof \Vidal\MainBundle\MailBounceHandler\Models\Mail) {
                continue;
            }
            foreach ($mail->getRecipients() as $recipient) {
                if (!$recipient instanceof \Vidal\MainBundle\MailBounceHandler\Models\Recipient) {
                    continue;
                }
                $resultStr = $recipient->getEmail()
                    . ' action:' . $recipient->getAction()
                    . ' bounceType:' . $recipient->getBounceType()
                    . ' bounceCat:' . $recipient->getBounceCat()
                    . ' status:' . $recipient->getStatus();

                if ($user = $selectQuery->setParameter('email', $recipient->getEmail())->getOneOrNullResult()) {
                    /** @var $user User */
                    echo '+++ USER FOUND: ' . $resultStr . PHP_EOL;
                    $user->setMailAction($recipient->getAction());
                    $user->setMailStatus($recipient->getStatus());
                    $user->setMailBounceCat($recipient->getBounceCat());
                    $user->setMailBounceType($recipient->getBounceType());
                    if ($recipient->getBounceType() == 'hard') {
                        echo '... hard blocked!' . PHP_EOL;
                        $user->setMailDelete(true);
                        $user->setMailDeleteCounter($user->getMailDeleteCounter() + 1);
                        $em->getRepository('VidalMainBundle:User')->setDeliveryLogFailed($user);
                    }
                    $em->flush($user);
                }
                else {
                    echo '--- not found ' . $resultStr . PHP_EOL;
                }
            }
        }
    }
}
