<?php

namespace Foxkit\Installer\Helper;

use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class InstallerIO extends ConsoleIO
{
    const REGEX = '/((?<=<error>).+(?=<\/error>))|(\[.+Exception\])/';

    /**
     * {@inheritdoc}
     */
    public function __construct(InputInterface $input = null, OutputInterface $output = null, HelperSet $helperSet = null)
    {
        parent::__construct($input ?: new ArrayInput([]), $output ?: new StreamOutput(fopen('php://output', 'w')), $helperSet ?: new HelperSet());
        if (PHP_SAPI != 'cli') {
            ob_implicit_flush(true);
            @ob_end_flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeErr($messages, $newline = true)
    {
        if (is_string($messages)) {
            if (preg_match(self::REGEX, $messages, $matches)) {
                throw new \RuntimeException($matches[0]);
            }
        } elseif (is_array($messages)) {
            foreach ($messages as $message) {
                if (preg_match(self::REGEX, $message, $matches)) {
                    throw new \RuntimeException($matches[0]);
                }
            }
        }
        parent::writeError($messages, $newline);
    }
}
