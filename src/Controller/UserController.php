<?php

namespace App\Controller;

use App\Form\SignUpType;
use App\Persistence\Entity\User;
use App\Security\SignInAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class UserController extends AbstractController
{
    public const SIGN_IN_ROUTE = 'app_sign_in';
    public const SIGN_OUT_ROUTE = 'app_sign_out';
    public const SIGN_UP_ROUTE = 'app_sign_up';

    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly SignInAuthenticator         $authenticator,
        private readonly UserAuthenticatorInterface  $userAuthenticator,
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    #[Route('/sign-in', name: self::SIGN_IN_ROUTE)]
    public function signIn(AuthenticationUtils $utils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute(QuizController::QUIZ_LIST_ROUTE);
         }

        return $this->render('user/sign-in.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/sign-out', name: self::SIGN_OUT_ROUTE)]
    public function signOut(): Response
    {
        throw new \LogicException('This action should be intercepted');
    }

    #[Route('/sign-up', name: self::SIGN_UP_ROUTE)]
    public function signUp(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(SignUpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->hasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            return $this->userAuthenticator->authenticateUser(
                $user,
                $this->authenticator,
                $request
            );
        }

        return $this->render('user/sign-up.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}