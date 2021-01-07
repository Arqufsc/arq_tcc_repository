<?php

class tcc_trb
{
    private $id;
    private $titulo;
    private $ano;
    private $semestre;
    private $tema_id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }
}