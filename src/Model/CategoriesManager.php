<?php

namespace App\Model;

class CategoriesManager extends AbstractManager
{
    /* Get element about book to choose in form */
    public function selectAll(): array
    {
        $statement = 'SELECT id AS category_id, name AS category_name  FROM category;';
        return $this->pdo->query($statement)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /* Insert element aboot the book into bdd */
    public function setCategory(array $information): void
    {
        $statement = $this->pdo->prepare("INSERT INTO category (name) VALUES (:category_name)");
        $statement->bindValue(":category_name", $information['category_name'], \PDO::PARAM_STR);
        $statement->execute();
    }
    

    

}

