<?php


namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @ORM\ManyToMany(targetEntity="IntraBundle\Entity\Matieres", mappedBy="users")
     */
    protected $matieres;

    /**
     * @ORM\OneToMany(targetEntity="IntraBundle\Entity\Notes", mappedBy="user")
     */
    private $notes;


    /**
     * Add matiere
     *
     * @param \IntraBundle\Entity\Matieres $matiere
     *
     * @return User
     */
    public function addMatiere(\IntraBundle\Entity\Matieres $matiere)
    {
        $this->matieres[] = $matiere;

        return $this;
    }

    /**
     * Remove matiere
     *
     * @param \IntraBundle\Entity\Matieres $matiere
     */
    public function removeMatiere(\IntraBundle\Entity\Matieres $matiere)
    {
        $this->matieres->removeElement($matiere);
    }

    /**
     * Get matieres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatieres()
    {
        return $this->matieres;
    }

    /**
     * Add note
     *
     * @param \IntraBundle\Entity\Notes $note
     *
     * @return User
     */
    public function addNote(\IntraBundle\Entity\Notes $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \IntraBundle\Entity\Notes $note
     */
    public function removeNote(\IntraBundle\Entity\Notes $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
