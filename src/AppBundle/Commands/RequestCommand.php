<?php

namespace AppBundle\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class RequestCommand
 * @package AppBundle\Commands
 * @SuppressWarnings(PHPMD)
 */
class RequestCommand extends Command
{
    const DEALER_HAND = 'Рука дилера.';
    const PLAYER_HAND = 'Ваша рука.';

    /**
     * @var array
     */
    private $deck = [];

    /**
     * @var array
     */
    private $playerHand = [];

    /**
     * @var array
     */
    private $dealerHand = [];

    /**
     * @var array
     */
    private $faces = [
        "Два" => 2,
        "Три" => 3,
        "Четыре" => 4,
        "Пять" => 5,
        "Шесть" => 6,
        "Семь" => 7,
        "Восемь" => 8,
        "Девять" => 9,
        "Десять" => 10,
        "Валет" => 2,
        "Дама" => 3,
        "Король" => 4,
        "Туз" => 1
    ];

    /**
     * @var array
     */
    private $suits = [
        "Пики",
        "Червы",
        "Трефы",
        "Бубны"
    ];

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('play')
            ->setDescription('Make \'request\'');
    }

    /**
     * @param InputInterface  $input  Input.
     * @param OutputInterface $output Output.
     * @return integer|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');


        if (empty($this->deck) && empty($playerHand) && empty($dealerHand)) {
            $this->makeDeck();
            $this->playerHand = $this->makeHand();
            $this->makeDealerHand();
        }

        $playerHandValue = $this->evaluateHand($this->playerHand);
        $dealerHandValue = $this->evaluateHand($this->dealerHand);

        $this->printHand($output, $playerHandValue, self::PLAYER_HAND, $this->playerHand);

        if ($playerHandValue > 21 || $dealerHandValue === 21) {
            $output->writeln('<error>Вы проиграли!</error>');
            $output->writeln('');
            $this->printHand($output, $dealerHandValue, self::DEALER_HAND, $this->dealerHand);

            return;
        }

        if ($dealerHandValue > 21 || $playerHandValue === 21) {
            $output->writeln('<info>Вы выиграли!</info>');
            $output->writeln('');
            $this->printHand($output, $dealerHandValue, self::DEALER_HAND, $this->dealerHand);

            return;
        }

        $question = new ConfirmationQuestion('Еще? (Y/n): ', false,
            '/^(y|j)/i');

        $answer = $helper->ask($input, $output, $question);

        if ($answer === true) {
            $this->playerHand = $this->giveCard($this->playerHand);
            $this->execute($input, $output);
        } else {
            $result = $this->getWinner($playerHandValue, $dealerHandValue);
            if ($result === false) {
                $output->writeln('<info>Ничья, добираем по карте!</info>');
                $output->writeln('');
                $this->playerHand = $this->giveCard($this->playerHand);
                $this->dealerHand = $this->giveCard($this->dealerHand);
                $this->execute($input, $output);
            }
            $this->printHand($output, $dealerHandValue, self::DEALER_HAND, $this->dealerHand);
            $output->writeln($result);
        }
    }

    /**
     * @param OutputInterface $output    Output.
     * @param integer         $handValue Hand value.
     * @param string          $message   Message.
     * @param array           $deck      Deck.
     */
    protected function printHand(OutputInterface $output, int $handValue, string $message, array $deck)
    {
        $output->writeln('<options=bold,underscore>' . $message . '</>');
        $output->writeln('');
        foreach ($deck as $key => $card) {
            $output->writeln('Карта: <info>' . $card['face'] . ' </info>Масть: <comment>' . $card['suit'] . '</comment>');
        }
        $output->writeln('<error>Значение карт в руке: ' . $handValue . '</error>');
        $output->writeln('');
        $output->writeln('<fg=black;bg=cyan>======================================================================</>');

    }

    /**
     * @return void
     */
    protected function makeDealerHand()
    {
        $this->dealerHand = $this->giveCard($this->dealerHand);
        $dealerHandValue = $this->evaluateHand($this->dealerHand);

        if ($dealerHandValue < 15) {
            $this->makeDealerHand();
        }
    }

    /**
     * @param array $hand Hand.
     * @return int
     */
    protected function evaluateHand(array $hand)
    {
        $value = 0;
        foreach ($hand as $card) {
            if ($value > 11 && $card['face'] == 'Туз') {
                $value = $value + 1;
            } else {
                $value = intval($value) + intval($this->faces[$card['face']]);
            }
        }
        return $value;
    }

    /**
     * @param integer $playerDeckValue Value.
     * @param integer $dealerDeckValue Value.
     * @return bool|string
     */
    protected function getWinner(int $playerDeckValue, int $dealerDeckValue)
    {
        $player = 21 - $playerDeckValue;
        $dealer = 21 - $dealerDeckValue;

        if ($player > $dealer) {
            return '<error>Вы проиграли!</error>';
        }

        if ($player < $dealer) {
            return '<info>Вы выиграли!</info>';
        }

        return false;
    }

    /**
     * @param array $hand Hand.
     * @return array
     */
    protected function giveCard(array $hand)
    {
        shuffle($this->deck);
        $hand[] = array_shift($this->deck);

        return $hand;
    }

    /**
     * @return array
     */
    protected function makeDeck()
    {
        foreach ($this->suits as $suit) {
            $keys = array_keys($this->faces);
            foreach ($keys as $face) {
                $this->deck[] = array('face' => $face, 'suit' => $suit);
            }
        }

        return $this->deck;
    }

    /**
     * @return array
     */
    protected function makeHand()
    {
        $hand = [];
        shuffle($this->deck);

        for ($i = 0; $i < 2; $i++) {
            $hand[] = array_shift($this->deck);
        }

        return $hand;
    }
}