<?php


namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\ProfileType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, TranslatorInterface $translator, TokenGeneratorInterface $tokenGenerator)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->trans = $translator;
        $this->tokenGen = $tokenGenerator;
    }

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/register", name="security_register")
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($request->isMethod(Request::METHOD_POST) && $form->isSubmitted() && $form->isValid()){
            try{
                $token = $this->tokenGen->generateToken();
                $password = $this->encoder->encodePassword($user, $user->getPassword());
                $user->setResetToken($token);
                $user->setPassword($password);
                $user->setRoles([User::ROLE_USER]);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', $this->trans->trans('admin.users.create.message'));

                $url = $this->generateUrl('security_account_confirm', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                exec("php ../bin/console app:send-email --subject=registration --email=".$user->getEmail()." --url=$url >> ../var/log/email.log&");

            }catch (\Exception $e){

            }

        }
        return $this->render('backend/security/register.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils){

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('backend/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }


    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/profile", name="security_profile", methods={"GET", "POST"})
     */
    public function profile(Request $request){

        $userId = $this->getUser()->getId();

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user){
            throw new NotFoundHttpException($this->trans->trans('admin.user.notfound'));
        }
        $oldPassword = $user->getPassword();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($request->isMethod(Request::METHOD_POST) && $form->isSubmitted() && $form->isValid()){

            if (!empty($user->getPassword())){
                $password = $this->encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            }else{
                $user->setPassword($oldPassword);
            }

            $this->entityManager->flush();
            $this->addFlash('success', $this->trans->trans('admin.profile.update.message'));
        }

        return $this->render('backend/security/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="password-recover", name="security_password_recover", methods={"GET"})
     */
    public function passwordRecover(Request $request)
    {
        return $this->render('backend/security/password-recover.html.twig');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="reset-password/{token}", name="security_password_reset", methods={"GET"})
     */
    public function resetPassword(Request $request, string $token)
    {
        return $this->render('backend/security/password-reset.html.twig', ['token' => $token]);
    }

    /**
     * @param Request $request
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="account-confirm/{token}", name="security_account_confirm", methods={"GET"})
     */
    public function accountConfirmation(Request $request, string $token)
    {

        $status = false;
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->loadUserByToken($token);
        if ($user)
        {
            $user->setResetToken(null);
            $user->setEnabled(true);

            $status = true;
            $this->entityManager->flush();

            $this->addFlash('info', $this->trans->trans('admin.account.confirmation.success'));
        }else{
            $this->addFlash('error', $this->trans->trans('admin.user.notfound'));
        }


        return $this->render('backend/security/account-confirmation.html.twig', ['token' => $token, 'status' => $status]);
    }

    /**
     * logout
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/logout", name="security_logout")
     */
    public function logout()
    {

    }

}
