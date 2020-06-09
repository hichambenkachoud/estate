<?php


namespace App\Command;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Loader\LoaderInterface;

class EmailCommand extends Command
{

    protected $mailer;
    protected $trans;
    protected $container;
    protected $entity_manager;
    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, ContainerInterface $container, EntityManagerInterface $entityManager, string $name = null)
    {
        $this->mailer = $mailer;
        $this->trans = $translator;
        $this->container = $container;
        $this->entity_manager = $entityManager;
        parent::__construct($name);
    }

    protected static $defaultName = 'app:send-email';

    protected function configure()
    {
        $this
            ->setDescription('Send emails.')
            ->setHelp('This command allows you to send emails.')
            ->addOption('subject', null, InputOption::VALUE_REQUIRED , 'User Id')
            ->addOption('email', null, InputOption::VALUE_REQUIRED , 'User Id')
            ->addOption('url', null, InputOption:: VALUE_OPTIONAL , 'Url registration or resetPassword')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subject = $input->getOption('subject');
        $email = $input->getOption('email');
        $url = $input->getOption('url');

        $output->writeln("============================ ".date('Y-m-d H:i:s')." ============================");
        $output->writeln("Subject : ".$subject);
        $output->writeln("Email : ".$email);
        $output->writeln("Url : ".$url);

        $user = $this->entity_manager->getRepository(User::class)->loadUserByUsername($email);
        $emailToSend = new Email();
        if ($subject == 'registration')
        {
            $emailToSend
                ->from('no-reply@hichambenkachoud.com')
                ->to($email)
                ->subject($this->trans->trans('admin.email.registration'))
                ->html($this->container->get('twig')->render('backend/emails/registration-email.html.twig',
                    [
                        'user' => $user,
                        'url' => $url
                    ]));
        }elseif ($subject == 'resetPassword')
        {
            $emailToSend
                ->from('no-reply@hichambenkachoud.com')
                ->to($email)
                ->subject($this->trans->trans('admin.password.recover'))
                ->html($this->container->get('twig')->render('backend/emails/reset-password-email.html.twig',
                    [
                        'user' => $user,
                        'url' => $url
                    ]));
        }elseif ($subject == 'advertValidate'){

        }



        $this->mailer->send($emailToSend);

        return 0;
    }

}
