<?php


namespace App\Controller;


use App\Entity\Members;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private $encoder;
    private $trans;
    private $tokenGen;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, TranslatorInterface $translator, TokenGeneratorInterface $tokenGenerator, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->trans = $translator;
        $this->tokenGen = $tokenGenerator;
        $this->logger = $logger;
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/se_connecter", name="front_security_login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('frontend/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/s'inscrire", name="front_security_register", methods={"GET", "POST"})
     */

    public function register(Request $request)
    {
        $member = new Members();
        $form = $this->createForm(RegisterType::class, $member);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST) && $form->isValid()){
            $token = $this->tokenGen->generateToken();

            try{
                $member->setConfirmationToken($token);
                $member->setPassword($this->encoder->encodePassword($member, $member->getPassword()));
                $this->entityManager->persist($member);
                $this->entityManager->flush();
                $this->addFlash('success', $this->trans->trans('front.register.message.success'));

                $url = $this->generateUrl('front_security_confirm_account', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                exec("php ../bin/console app:send-email --subject=registration --email=".$member->getEmail()." --url=$url --lang=".$request->getLocale().">> ../var/log/register.log&");
            }catch (\Exception $e){
                $this->logger->info(json_encode($e));
            }

        }
        return $this->render('frontend/security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="/confirmer_l'inscription/{token}" , name="front_security_confirm_account", methods={"GET"})
     */
    public function confirmAccount($token = null)
    {
        /** @var Members $member */
        $member = $this->entityManager->getRepository(Members::class)->loadMemberByToken($token);
        if ($member)
        {
            $member->setConfirmationToken(null);
            $member->setEnabled(true);

            $this->entityManager->flush();

            $this->addFlash('info', $this->trans->trans('front.register.confirmation.success'));
        }else{
            $this->addFlash('error', $this->trans->trans('front.register.confirmation.failed'));
        }

        return $this->render('frontend/security/confirmation.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/mot_de_passe_oublié", name="front_security_password_forget", methods={"GET"})
     */
    public function forgetPassword()
    {
        return $this->render('frontend/security/forgetPassword.html.twig');
    }
    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/réinitialiser_le_mot_de_passe/{token}", name="front_security_password_reset", methods={"GET"})
     */
    public function resetPassword($token = null)
    {
        $valid = false;
        /** @var Members $member */
        $member = $this->entityManager->getRepository(Members::class)->loadMemberByToken($token);
        if ($member)
        {
          $valid = true;
        }else{
            $this->addFlash('error', $this->trans->trans('front.password.reset.failed'));
        }
        return $this->render('frontend/security/resetPassword.html.twig', ['token' => $token, 'valid' => $valid]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/mon_profil", name="front_security_profile")
     */
    public function profile()
    {
        return $this->render('frontend/security/profile.html.twig');
    }

    /**
     * logout
     * @Route(path="/logout", name="front_security_logout", methods={"GET"})
     */
    public function logout()
    {
    }
}
