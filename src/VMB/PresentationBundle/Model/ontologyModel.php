<?php
namespace VMB\PresentationBundle\Model;

class ontologyModel
{
	protected $con;
	protected $folder;

	public function __construct($db, $kernel) {
		$this->con = $db;
		$this->folder = $kernel->getRootDir().'/../web/bundles/telecomvmb/json/';
	}	

	public function getOntologies() {
        $arr = $this->con->fetchAll('SELECT id, name FROM ontologies');
        return $arr;
    }

    public function getOntology($id) {
        $statement = $this->con->executeQuery('SELECT * FROM ontologies WHERE id = ?', array($id));
        $arr = $statement->fetchAll();
        if (is_null($arr) || empty($arr)) return null;

        return $arr[0];
    }

    public function newOntology() {
        $this->con->insert('ontologies', array('name' => 'name') );
        $id = $this->con->lastInsertId();
        file_put_contents($this->getAbsoluteIndexFile($id), '{}');
        file_put_contents($this->getAbsoluteOntologyFile($id), '[]');
        return $id;
    }

    public function getIndexFile ($id) {
        return "index".$id.".json";
    }

    public function getOntologyFile ($id) {
        return "ontology".$id.".json";
    }
    public function getAbsoluteIndexFile($id) {
        return $this->folder.$this->getIndexFile($id);
    }

    public function getAbsoluteOntologyFile($id) {
        return $this->folder.$this->getOntologyFile($id);
    }

}
