<?php


namespace App\Controller\Admin;


use App\Entity\Adverts;
use App\Entity\Members;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AjaxController
 * @package App\Controller\Admin
 * @Route(path="/ajax")
 */
class AjaxController extends AbstractController
{

    private $entityManager;
    private $encoder;
    private $trans;
    private $mailer;
    private $tokenGenerator;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, TranslatorInterface $translator, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->trans = $translator;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }


    /**
     * generate password
     * @Route(path="/generate-password", name="security_generate_password", methods={"POST"})
     */
    public function generatePassword()
    {
        $data = $this->_checkData();
        $email = isset($data['email']) ? trim($data['email']) : null;

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->loadUserByUsername($email);
        if (!$user)
        {
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.user.notfound')];
            return new Response(json_encode($response));
        }

        if (!$user->getEnabled()){
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.user.notconfirm')];
            return new Response(json_encode($response));
        }

        $token = $this->tokenGenerator->generateToken();
        try{
            $user->setResetToken($token);
            $this->entityManager->flush();
        }catch (\Exception $e){
            $response["code"] = -2;
            $response["response"] = ["message" => $this->trans->trans('admin.error.505')];
            return new Response(json_encode($response));
        }

        $url = $this->generateUrl('security_password_reset', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

        exec("php ../bin/console app:send-email --subject=resetPassword --email=".$user->getEmail()." --url=$url >> ../var/log/email.log&");

        $response["code"] = 0;
        $response["response"] = ["message"=> $this->trans->trans('admin.password.reset.email')];
        return new Response(json_encode($response));

    }
    /**
     * reset password
     * @Route(path="/reset-password", name="security_reset_password", methods={"POST"})
     */
    public function resetPassword()
    {
        $data = $this->_checkData();
        $password = isset($data['password']) ? trim($data['password']) : null;
        $token = isset($data['token']) ? trim($data['token']) : null;

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->loadUserByToken($token);
        if (!$user)
        {
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.user.notfound')];
            return new Response(json_encode($response));
        }

        try{
            $user->setPassword($this->encoder->encodePassword($user, $password));
            $user->setResetToken(null);
            $this->entityManager->flush();
        }catch (\Exception $e){
            $response["code"] = -2;
            $response["response"] = ["message" => $this->trans->trans('admin.error.505')];
            return new Response(json_encode($response));
        }

        $response["code"] = 0;
        $response["response"] = ["message"=> $this->trans->trans('admin.password.reset.success')];
        return new Response(json_encode($response));

    }


    /**
     * valid advert
     * @Route("/valid", name="ajax_adverts_valid", methods={"POST"})
     */
    public function valid()
    {
        $data = $this->_checkData();
        $advertId = isset($data['advertId']) ? intval($data['advertId']) : 0;
        /** @var Adverts $advert */
        $advert = $this->entityManager->getRepository(Adverts::class)->find($advertId);
        if (!$advert){
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.advert.notfound')];
            return new Response(json_encode($response));
        }
        /** @var Members $member */
        $member = $advert->getMember();

        try{
            $advert->setValid(true);
            $advert->setRefused(false);
            $this->entityManager->flush();

        }catch (\Exception $e){

        }
        exec("php ../bin/console app:send-email --subject=advertValidate --email=".$member->getEmail()." >> ../var/log/email.log&");

        $response["code"] = 0;
        $response["response"] = ["message"=> $this->trans->trans('admin.advert.valid.success')];
        return new Response(json_encode($response));
    }
    /**
     * valid advert
     * @Route("/refuse", name="ajax_adverts_refuse", methods={"POST"})
     */
    public function refuse()
    {
        $data = $this->_checkData();
        $advertId = isset($data['advertId']) ? intval($data['advertId']) : 0;
        /** @var Adverts $advert */
        $advert = $this->entityManager->getRepository(Adverts::class)->find($advertId);
        if (!$advert){
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.advert.notfound')];
            return new Response(json_encode($response));
        }

        try{
            $advert->setRefused(true);
            $advert->setValid(false);
            $this->entityManager->flush();

        }catch (\Exception $e){

        }
        //exec("php ../bin/console app:send-email --subject=advertValidate --email=".$member->getEmail()." >> ../var/log/email.log&");

        $response["code"] = 0;
        $response["response"] = ["message"=> $this->trans->trans('admin.advert.refuse.success')];
        return new Response(json_encode($response));
    }

    /**
     * valid advert
     * @Route("/delete", name="ajax_adverts_delete", methods={"DELETE"})
     */
    public function delete()
    {
        $data = $this->_checkData();
        $advertId = isset($data['advertId']) ? intval($data['advertId']) : 0;
        $token = isset($data['token']) ? $data['token'] : null;
        /** @var Adverts $advert */
        $advert = $this->entityManager->getRepository(Adverts::class)->find($advertId);
        if (!$advert){
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.advert.notfound')];
            return new Response(json_encode($response));
        }

        try{
            if ($this->isCsrfTokenValid('delete'.$advert->getId(), $token)) {
                $this->entityManager->remove($advert);
                $this->entityManager->flush();

            }else{
                $response["code"] = -1;
                $response["response"] = ["message"=> $this->trans->trans('admin.form.invalid')];
                return new Response(json_encode($response));
            }

        }catch (\Exception $e){

        }
        //exec("php ../bin/console app:send-email --subject=advertValidate --email=".$member->getEmail()." >> ../var/log/email.log&");

        $response["code"] = 0;
        $response["response"] = ["message"=> $this->trans->trans('admin.advert.delete.success')];
        return new Response(json_encode($response));
    }

    private function _checkData()
    {
        $data = json_decode(file_get_contents('php://input'), TRUE);
        if ($data == null)
        {
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.form.empty')];
            return new Response(json_encode($response));
        }
        return $data;
    }
}
