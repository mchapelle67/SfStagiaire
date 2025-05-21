<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    // afficher les stagiaires non inscrits à une session
    public function findStudentNoRegister($sessionId)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // séléctionner les stagiaires dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Student', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');

        $sub = $em->createQueryBuilder();
        // séléctionner les stagiaires qui ne sont pas dans le resultat precedent 
        // on obtient alors les stagiaires non inscrits à la session
        $sub->select('st')
            ->from('App\Entity\Student', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            // requete paramètre
            ->setParameter('id', $sessionId)
            // trier les stagiaires par nom de famille
            ->orderBy('st.surname', 'ASC');

        $query = $sub->getQuery();
        return $query->getResult();
    }

    // Afficher les modules non inscrits à une session
    public function findModuleNotInSession($sessionId)
    {
        $em = $this->getEntityManager();

        // Sous-requête : sélectionner les modules déjà inscrits à la session
        $qb = $em->createQueryBuilder();
        // Sous-requête : sélectionner les modules déjà inscrits au programme de la session
        $qb->select('m.id')
            ->from('App\Entity\Session', 's')
            ->leftJoin('s.programs', 'p')
            ->leftJoin('p.module', 'm')
            ->where('s.id = :id');

        // Requête principale : sélectionner les modules qui ne sont pas dans la sous-requête
        $sub = $em->createQueryBuilder();
        $sub->select('mod')
            ->from('App\Entity\Module', 'mod')
            ->where($sub->expr()->notIn('mod.id', $qb->getDQL()))
            ->setParameter('id', $sessionId);

        $query = $sub->getQuery();
        return $query->getResult();
    }


    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
