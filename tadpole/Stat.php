<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Tadpole;

use MongoDB\Client;

class Stat
{
    protected $table = 'tadpole';

    protected $mongo;


    public function __construct()
    {
        $this->setMongo();
        $db = $this->mongo->pad;    //init db

        $table = $this->table;    //get table name

        $this->model = $db->$table;

    }

    public function setGender($id)
    {
        $gender = rand(0, 1);

        $this->model->updateOne(
            ['id' => $id],
            [
                '$set' => [
                    'gender'      => $gender,
                ]
            ],
            ['upsert' => true]
        );
    }

    public function getGender($id)
    {
        return $this->model->findOne(['id' => $id])->gender;
    }

    /**
     * set mongo db instance
     */
    protected function setMongo()
    {
        $mongo = new Client();
        $this->mongo = $mongo;
    }
}