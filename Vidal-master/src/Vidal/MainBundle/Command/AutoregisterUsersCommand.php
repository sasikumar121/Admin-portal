<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Vidal\MainBundle\Entity\City;
use Vidal\MainBundle\Entity\Specialty;
use Vidal\MainBundle\Entity\User;

class AutoregisterUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:autoregister_users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
        set_time_limit(0);
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:autoregister_users started');

        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
		$em->createQuery("
			UPDATE VidalMainBundle:User u SET u.autoregister = FALSE
		")->execute();

		$file = fopen(__DIR__ . '/Data/users.csv', 'r');

		$i = 0;
		$noSpecialty = array();
        $noCity = array();

        $testMode = false;

        $blankCityId = 5568853;
        $blankSpecId = 110;
        /** @var City $blankCity */
        $blankCity = $em->getRepository("VidalMainBundle:City")->findOneById($blankCityId);
        /** @var Specialty $blankSpec */
        $blankSpec = $em->getRepository("VidalMainBundle:Specialty")->findOneById($blankSpecId);
		$usersWithoutSpec = 0;
		$usersWithoutCity = 0;

		while (($line = fgetcsv($file)) !== FALSE) {
			$i++;
			$row = explode(';', $line[0]);
            $output->writeln('... ' . $i);
			$userFound = false;

			# 0-fio 1-org 2-город 3-dolz 4-spec 5-spec2 6-telef 7-email
            # ФИО;Организация;Город;Должность;Специальность;2-я специальность;Телефон;E-mail

			if (empty($row[7])) {
				continue;
			}

			# User
			$username = trim($row[7]);

            /** @var User $user */
			if ($user = $em->getRepository('VidalMainBundle:User')->findOneByUsername($username)) {
				$userFound = true;
				$user->setAutoregister(true);
			}
			else {
				$userFound = false;
				$user = new User();
				$user->setUsername($username);
				$user->setAutoregister(true);
			}

			# FIO
			$fio = trim($row[0]);
			$names = explode(' ', $fio);
			$lastName = $names[0];

			if (strpos($fio, '.') !== false) {
				$secondNames = str_replace(' ', '', $names[1]);
				$secondNames = trim($secondNames, '.');
				$secondNames = explode('.', $secondNames);
				$firstName = $secondNames[0] . '.';
				$surName = $secondNames[1] . '.';
			}
			else {
				$firstName = isset($names[1]) ? $names[1] : null;
				$surName = isset($names[2]) ? $names[2] : null;
			}

            if (!$userFound) {
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setSurName($surName);
            }
			//$output->writeln($lastName . ' ' . $firstName . ' ' . $surName);

			# Password
			$digits = 4;
			$pw =  rand(pow(10, $digits-1), pow(10, $digits)-1);

            if (!$userFound) {
                $user->setPassword($pw);
            }

			# Phone
			if (!empty($row[6])) {
				$phone = trim($row[5]);
				$user->setPhone($phone);
			}

			# Job
			if (!empty($row[1])) {
				$jobPlace = trim($row[1]);
				$user->setJobPlace($jobPlace);
			}
			if (!empty($row[3])) {
				$jobPosition = trim($row[3]);
				$user->setJobPosition($jobPosition);
			}

			# Specialty
			if (!empty($row[4])) {
				$specName = trim($row[4]);
				if ($spec = $em->getRepository('VidalMainBundle:Specialty')->findByName($specName)) {
					$user->setPrimarySpecialty($spec);
				}
				else {
				    $user->setPrimarySpecialty($blankSpec);
                    $user->setAutoregisterSpec($specName);
					$noSpecialty[] = $specName;
					$usersWithoutSpec++;
				}
			}

            # Specialty-2
            if (!empty($row[5])) {
                $specName = trim($row[4]);
                if ($spec = $em->getRepository('VidalMainBundle:Specialty')->findByName($specName)) {
                    $user->setSecondarySpecialty($spec);
                }
            }

            # City
            if (!empty($row[2])) {
                $name = trim($row[2]);
                if ($model = $em->getRepository('VidalMainBundle:City')->findByName($name)) {
                    $user->setCity($model);
                }
				elseif ($model = $em->getRepository('VidalMainBundle:City')->findAnyByName($name)) {
					$user->setCity($model);
				}
                else {
                    $user->setCity($blankCity);
                    $user->setAutoregisterCity($name);
                    $noCity[] = $name;
					$usersWithoutCity++;
                }
            }

			if ($userFound == false) {
				$em->persist($user);
			}

			$user->refreshHash();

            if ($testMode == false) {
                $em->flush($user);
            }
		}
		fclose($file);

        if ($testMode) {
            $noSpecialty = array_unique($noSpecialty);
            $output->writeln('No specialty: ' . implode(', ', $noSpecialty));
            $noCity = array_unique($noCity);
            $output->writeln('No city: ' . implode(', ', $noCity));
			$output->writeln('Total without specialty: ' . $usersWithoutSpec);
			$output->writeln('Total without city: ' . $usersWithoutCity);
        }

		$output->writeln('+++ vidal:autoregister_users completed!');
    }
}
