<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class StaticCardsCommand extends ContainerAwareCommand
{

	protected function configure()
	{
		$this
		->setName('app:static')
		->setDescription('Generate static card data files for all locales')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("Generate cards json.");
		$doctrine = $this->getContainer()->get('doctrine');

		$supported_locales = $this->getContainer()->getParameter('supported_locales');
		$default_locale = $this->getContainer()->getParameter('locale');
		foreach($supported_locales as $supported_locale) {
			$doctrine->getRepository('AppBundle:Card')->setDefaultLocale($supported_locale);
			$list_cards = $doctrine->getRepository('AppBundle:Card')->findAll();
			// build the file
			$cards = array();
			/* @var $card \AppBundle\Entity\Card */
			foreach($list_cards as $card) {
				$cards[] = $this->getContainer()->get('cards_data')->getCardInfo($card, true, $supported_locale);
			}
			$content = json_encode($cards);
			$webdir = $this->getContainer()->get('kernel')->getRootDir() . "/../web";
			file_put_contents($webdir."/cards-all-".$supported_locale.".json", $content);

			$list_cards = $doctrine->getRepository('AppBundle:Card')->findAllWithoutEncounter();
			// build the file
			$cards = array();
			/* @var $card \AppBundle\Entity\Card */
			foreach($list_cards as $card) {
				$cards[] = $this->getContainer()->get('cards_data')->getCardInfo($card, true, $supported_locale);
			}
			$content = json_encode($cards);
			$webdir = $this->getContainer()->get('kernel')->getRootDir() . "/../web";
			file_put_contents($webdir."/cards-player-".$supported_locale.".json", $content);
		}
		$output->writeln("Done.");
	}

}
