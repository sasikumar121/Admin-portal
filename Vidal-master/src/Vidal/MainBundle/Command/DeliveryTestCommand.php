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

class DeliveryTestCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:delivery-test');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		ini_set('max_input_time', 0);
		ini_set('memory_limit', -1);

		$output->writeln("=> Sending: in progress to 7binary@bk.ru");
		$this->sendTo(array('binarya@yandex.ru'), false);

		$output->writeln('=> Completed!');
		return true;
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
