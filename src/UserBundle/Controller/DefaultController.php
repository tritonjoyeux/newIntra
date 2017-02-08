<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\UserInfos;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function homeAction()
    {
        if($this->getUser() != null) {
            if ($this->getUser()->hasRole('ROLE_ADMIN')) {
                return $this->redirectToRoute("intra_matieres_allmatieres");
            } else if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
                return $this->redirectToRoute("intra_admin_adminhome");
            } else {
                return $this->redirectToRoute("intra_matieres_allmatieres");
            }
        }
        return $this->redirectToRoute("fos_user_security_login");
    }
}
