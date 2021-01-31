<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([],['createdAt' => 'DESC']);
        // dd($pins);
        return $this->render('pins/index.html.twig',[
            'pins' => $pins
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        // 404 voir customize error page symfony

        return $this->render('pins/show.html.twig',[
            'pin' => $pin
        ]);
    }

    /**
     * @Route("/pins/create"), name="app_pins_create", methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);
            // ->add('title', TextType::class)
            // ->add('description', TextareaType::class)
            // ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // $pin = $form->getData();
            // $pin = new Pin;
            // $pin->setTitle($data['title']);
            // $pin->setDescription($data['description']);
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/pins/{id<[0-9]+>}/edit"), name="app_pins_edit", methods="GET|PUT")
     */
    public function edit(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {
        $form = $this->createForm(PinType::class, $pin, ['method' => 'PUT']);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig',[
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }
}


// gestion cache php (lent) : https://stackoverflow.com/questions/9854848/windows-7-php-symfony2-terribly-slow