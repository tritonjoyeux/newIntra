<?php

namespace IntraBundle\Controller;

use IntraBundle\Entity\Matieres;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin/matieres")
     */
    public function adminHomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('IntraBundle:Matieres');
        $matieres = $rep->findAll();
        return $this->render('IntraBundle:Admin:index.html.twig', array("title" => "Toutes les matieres" ,'matieres' => $matieres));
    }

    /**
     * @Route("/admin/matieres/create")
     */
    public function createMatiere(Request $request)
    {
        $matiere = new Matieres();

        $form = $this->createFormBuilder($matiere);
        $form->add("name", TextType::class)
            ->add('create', SubmitType::class);
        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matiere = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $rep = $em->getRepository('IntraBundle:Matieres');
            if(empty($rep->findBy(array('name' => $matiere->getName())))){
                $em->persist($matiere);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success', 'Matiere créée !');
            }else {
                $request->getSession()->getFlashBag()->add('error', 'Matiere existante !');
            }
        }
        return $this->render('IntraBundle:Matieres:create.html.twig', array("title" => "Créez une matieres" ,'form' => $form->createView()));
    }

    /**
     * @Route("/admin/matieres/delete/{id}")
     */
    public function deleteMatiereAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $matiere = $em->getRepository('IntraBundle:Matieres')->findOneBy(array('id' => $id));
        $em->remove($matiere);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'Matiere supprimée !');
        return $this->redirectToRoute("intra_admin_adminhome");
    }

    /**
     * @Route("/admin/matieres/update/{id}")
     */
    public function updateMatiereAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $matiere = $em->getRepository('IntraBundle:Matieres')->findOneBy(array('id' => $id));
        return $this->render('IntraBundle:Matieres:update.html.twig', array("title" => "Gerez une matieres" , 'matiere' => $matiere));
    }

    /**
     * @Route("/admin/matieres/remove/user/{id}/{uid}")
     */
    public function removeUserMatiereAction($id, $uid, Request $request){
        $em = $this->getDoctrine()->getManager();
        $matiere = $em->getRepository('IntraBundle:Matieres')->findOneBy(array('id' => $id));
        $user = $em->getRepository('UserBundle:User')->findOneBy(array('id' => $uid));
        $matiere->removeUser($user);
        $em->persist($matiere);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'Utilisateur supprimée de la matiere !');
        return $this->redirectToRoute("intra_admin_updatematiere", array('id' => $id));
    }

    /**
     * @Route("/admin/matieres/update/teacher/{id}")
     */
    public function changeTeacherAction($id, Request $request){
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery('SELECT u FROM UserBundle:User u WHERE u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_ADMIN"%');
        $users = $query->getResult();

        if($request->isMethod('post') && !empty($request->get('prof'))){
            $em = $this->getDoctrine()->getManager();
            $matiere = $em->getRepository('IntraBundle:Matieres')->findOneBy(array('id' => $id));

            foreach ($matiere->getUsers() as $user){
                if($user->hasRole('ROLE_ADMIN')){
                    $matiere->removeUser($user);
                }
            }

            $user = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $request->get('prof')));
            if($user->hasRole('ROLE_ADMIN')){
                $matiere->addUser($user);
                $em->persist($matiere);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success', 'Prof ajouté !');
            }else{
                $request->getSession()->getFlashBag()->add('error', 'Ce n\'est pas un professeur !');
            }
        }else{
            $request->getSession()->getFlashBag()->add('error', 'Champs vide !');
        }
        return $this->render('IntraBundle:Teacher:update.html.twig', array("title" => "Ajoutez un professeur", "users" => $users));
    }

    /**
     * @Route("/admin/matiere/adduser/{id}/{uid}")
     */
    public function addStudentsAction($id, $uid = false, Request $request){
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('UserBundle:User');
        $rep2 = $em->getRepository('IntraBundle:Matieres');
        if($uid != false){
            $user = $rep->findOneBy(array("id" => $uid));
            $matiere = $rep2->findOneBy(array('id' => $id));
            $matiere->addUser($user);
            $em->persist($matiere);
            try{
                $em->flush();
            }catch (\Exception $e){
                //nothing
            }
            $request->getSession()->getFlashBag()->add('success', 'Utilisateur ajouté !');
        }
        $users = $rep->findAll();
        return $this->render('IntraBundle:Matieres:users.html.twig', array("title" => "Gerez les utilisateurs", 'users' => $users, 'id' => $id));
    }

    //PROFILES

    /**
     * @Route("/admin/profile/roles")
     */
    public function changeRolesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('UserBundle:User');
        $users = $rep->findAll();
        return $this->render('IntraBundle:Admin:users.html.twig', array("title" => "Gerez les utilisateurs", 'users' => $users));
    }

    /**
     * @Route("/admin/profile/roles/update/{id}")
     */
    public function updateProfileAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('UserBundle:User');
        $user = $rep->findOneBy(array('id' => $id));
        $prof = false;

        return $this->render('IntraBundle:Admin:user.html.twig', array("title" => "Gerez un utilisateurs", 'user' => $user));
    }
}
