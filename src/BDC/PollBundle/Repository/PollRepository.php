<?php

namespace BDC\PollBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PollRepository extends EntityRepository {

    //se fija si existe otra respuesta igual perteneciente a la encuesta id_poll
    function duplicateAnswer($name, $id, $id_poll = null) {


        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        'SELECT p
                         FROM BDCPollBundle:Poll p
                         WHERE p.name = :name
                         AND p.id != :id
                         AND p.id_poll = :id_poll'
              
                )->setParameter('name', $name)->setParameter('id', intval($id))->setParameter('id_poll', intval($id_poll));

        $answers = $query->getResult();
        return count($answers) > 0;
    }
    
    function getGeneralStats($id_user = NULL) {
        
        $where = '';
        
        if($id_user){
            $where = 'WHERE p.id_user = '.$id_user;
        }
        
        $stmt = $this->getEntityManager()->getConnection()->prepare(
                'SELECT p.id,p.name,count(q.id) as total_questions, (SELECT count(v.id) FROM Vote v WHERE id_poll = p.id) as total_votes,
                (SELECT count(a.id) FROM Answer a WHERE a.id_poll = p.id) as total_answers
                FROM Poll p
                INNER JOIN Question q ON q.id_poll = p.id'.
                $where
                .' GROUP by p.id,p.name');
       
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    function getByMD5Id($md5_id) {
        $stmt = $this->getEntityManager()->getConnection()->prepare(
                "SELECT * from Poll WHERE MD5(id) = '$md5_id'");
       
        $stmt->execute();
        
        return $stmt->fetch();
    }

    

}
