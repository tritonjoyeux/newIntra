<?php

namespace IntraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;

class MatieresController extends Controller
{
    /**
     * @Route("/matieres")
     */
    public function allMatieresAction()
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('IntraBundle:Matieres');
        $matieres = $rep->findAll();
        return $this->render('IntraBundle:Matieres:index.html.twig', array("title" => "Toutes les matieres" ,'matieres' => $matieres));
    }

    /**
     * @Route("/matieres/my")
     */
    public function myMatieresAction()
    {
        $matieres = $this->getUser()->getMatieres();
        return $this->render('IntraBundle:Matieres:index.html.twig', array("title" => "Vos matieres", "matieres" => $matieres));
    }

    /**
     * @Route("/matieres/inscription")
     */
    public function inscriptionMatieresAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('IntraBundle:Matieres');
        if($request->isMethod('post')){
            $check = 0;
            $matieres = $request->get('matieres');
            foreach ($matieres as $matiere){
                $matiere = $rep->findOneBy(array('name' => $matiere));
                $matiere->addUser($this->getUser());
                try{
                    $em->flush($matiere);
                    $check++;
                }catch (\Exception $e){
                    //nothing
                }
            }
            if($check != 0)
                $request->getSession()->getFlashBag()->add('success', 'Vous etes inscrit !');
        }

        $matieres = $rep->findAll();

        $myMatieres = $this->getUser()->getMatieres();

        $id_myMatieres = array();
        foreach ($myMatieres as $matiere){
            array_push($id_myMatieres, $matiere->getId());
        }

        foreach ($matieres as $key => $matiere){
            if(in_array($matiere->getId(), $id_myMatieres)){
                unset($matieres[$key]);
            }
        }

        return $this->render('IntraBundle:Matieres:inscription.html.twig', array("title" => "Inscription" ,'matieres' => $matieres));
    }
}
