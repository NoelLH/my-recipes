<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
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

    /**
     * @ORM\ManyToMany(targetEntity="Recipe")
     * @ORM\JoinTable(name="stars")
     */
    protected $stars;

    /**
     * Add star
     *
     * @param \AppBundle\Entity\Recipe $star
     *
     * @return User
     */
    public function addStar(\AppBundle\Entity\Recipe $star)
    {
        $this->stars[] = $star;

        return $this;
    }

    /**
     * Remove star
     *
     * @param \AppBundle\Entity\Recipe $star
     */
    public function removeStar(\AppBundle\Entity\Recipe $star)
    {
        $this->stars->removeElement($star);
    }

    /**
     * Get stars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStars()
    {
        return $this->stars;
    }
}
