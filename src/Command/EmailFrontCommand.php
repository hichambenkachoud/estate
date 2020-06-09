<?php


namespace App\Command;


use App\Entity\Agency;
use App\Entity\Members;
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

class EmailFrontCommand extends Command
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

    protected static $defaultName = 'app:send-front-email';

    protected function configure()
    {
        $this
            ->setDescription('Send emails.')
            ->setHelp('This command allows you to send emails.')
            ->addOption('subject', null, InputOption::VALUE_REQUIRED , 'User Id')
            ->addOption('email', null, InputOption::VALUE_OPTIONAL , 'User Id')
            ->addOption('lang', null, InputOption::VALUE_OPTIONAL , 'Url registration or resetPassword')
            ->addOption('url', null, InputOption::VALUE_OPTIONAL , 'Url registration or resetPassword')
            ->addOption('emailContact', null, InputOption::VALUE_OPTIONAL , 'Email from')
            ->addOption('tel', null, InputOption::VALUE_OPTIONAL , 'Tel')
            ->addOption('fullName', null, InputOption::VALUE_OPTIONAL , 'Full Name')
            ->addOption('message', null, InputOption::VALUE_OPTIONAL , 'Message body')
            ->addOption('reason', null, InputOption::VALUE_OPTIONAL , 'Message reason')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subject = $input->getOption('subject');
        $email = $input->getOption('email');
        $url = $input->getOption('url');
        $lang = $input->getOption('lang');
        $emailContact = $input->getOption('emailContact');
        $tel = $input->getOption('tel');
        $fullName = $input->getOption('fullName');
        $message = $input->getOption('message');
        $reason = $input->getOption('reason');

        $output->writeln("============================ ".date('Y-m-d H:i:s')." ============================");
        $output->writeln("Subject : ".$subject);
        $output->writeln("Email : ".$email);
        $output->writeln("Url : ".$url);
        $output->writeln("emailContact : ".$emailContact);
        $this->container->get('translator')->setLocale($lang);

        $emailToSend = new Email();
        if ($subject == 'registration')
        {
            $user = $this->entity_manager->getRepository(Members::class)->loadMemberByUsername($email);
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
            $user = $this->entity_manager->getRepository(Members::class)->loadMemberByUsername($email);
            $emailToSend
                ->from('no-reply@hichambenkachoud.com')
                ->to($email)
                ->subject($this->trans->trans('admin.password.recover'))
                ->html($this->container->get('twig')->render('backend/emails/reset-password-email.html.twig',
                    [
                        'user' => $user,
                        'url' => $url
                    ]));
        }elseif ($subject == 'adverts'){
            $user = $this->entity_manager->getRepository(Members::class)->loadMemberByUsername($email);
            $emailToSend
                ->from('no-reply@hichambenkachoud.com')
                ->addTo($email)
                ->addTo('h.benkachoud@mundiapolis.ma')
                ->subject($this->trans->trans('front.contact-agent'))
                ->html($this->container->get('twig')->render('backend/emails/contact-agent-email.html.twig',
                    [
                        'user' => $user,
                        'lang' => $lang,
                        'message' => str_replace('_', ' ', $message ),
                        'tel' => $tel,
                        'fullName' => str_replace('_', ' ', $fullName ),
                        'email' => $emailContact
                    ]));
        } elseif ($subject == 'agency'){
            $agency = $this->entity_manager->getRepository(Agency::class)->loadAgencyByEmail($email);
            $emailToSend
                ->from('no-reply@hichambenkachoud.com')
                ->addTo($email)
                ->addTo('h.benkachoud@mundiapolis.ma')
                ->subject($this->trans->trans('front.contact-agent'))
                ->html($this->container->get('twig')->render('backend/emails/contact-agency-email.html.twig',
                    [
                        'agency' => $agency,
                        'lang' => $lang,
                        'message' => str_replace('_', ' ', $message ),
                        'tel' => $tel,
                        'fullName' => str_replace('_', ' ', $fullName ),
                        'email' => $emailContact
                    ]));
        } elseif ($subject == 'request'){
            $user = $this->entity_manager->getRepository(Members::class)->loadMemberByUsername($email);
            $emailToSend
                ->from('no-reply@hichambenkachoud.com')
                ->addTo($email)
                ->addTo('h.benkachoud@mundiapolis.ma')
                ->subject($this->trans->trans('front.contact-agent'))
                ->html($this->container->get('twig')->render('backend/emails/request-agent-email.html.twig',
                    [
                        'user' => $user,
                        'lang' => $lang,
                        'message' => str_replace('_', ' ', $message ),
                        'reason' => $reason,
                        'fullName' => str_replace('_', ' ', $fullName ),
                        'email' => $emailContact
                    ]));
        }elseif ($subject == 'contactus'){
            $emailToSend
                ->from('no-reply@hichambenkachoud.com')
                ->addTo('h.benkachoud@mundiapolis.ma')
                ->addTo('mohsine.lahlou1@gmail.com')
                ->subject($this->trans->trans('front.contact-us'))
                ->html($this->container->get('twig')->render('backend/emails/contact-us-email.html.twig',
                    [
                        'lang' => $lang,
                        'message' => str_replace('_', ' ', $message ),
                        'fullName' => str_replace('_', ' ', $fullName ),
                        'email' => $emailContact
                    ]));
        }

        $this->mailer->send($emailToSend);

        return 0;
    }

}
