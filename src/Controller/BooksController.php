<?php

namespace App\Controller;

use App\Model\FormProcessing;
use App\Model\StatusManager;
use App\Model\AuthorsManager;
use App\Model\EditorsManager;
use App\Model\FormatsManager;
use App\Model\CategoriesManager;
use App\Model\LocationsManager;

class BooksController extends AbstractController
{

    public function addBook(): string
    {
        /**
         * ! GET ELEMENT FOR LIST IN FORM
         */
        $authorsManager = new AuthorsManager();
        $authors = $authorsManager->selectAll('name');

        $editorsManager = new EditorsManager();
        $editors = $editorsManager->selectAll('name');

        $categoriesManager = new CategoriesManager();
        $categories = $categoriesManager->selectAll('name');

        $formatsManager = new FormatsManager();
        $formats = $formatsManager->selectAll('name');

        $locationsManager = new LocationsManager();
        $locations = $locationsManager->selectAll('name');

        $statusManager = new StatusManager();
        $status = $statusManager->selectAll('name');


        /**
         * ! PUT THE BOOK IN DBB
         */
        $formProcessing = new FormProcessing();
        $incompletForm = "";

        $errors = $formProcessing->verifyEmptyPost();

        if (empty($errors) && !empty($_FILES['avatar'])) {
            $path = $formProcessing->coverPage();
            $formProcessing->verifyAndAddBook($path);
        } else {
            $incompletForm = "Merci de remplir le formulaire";
        }

        return $this->twig->render('Books/addBook.html.twig', [
            'authors' => $authors, 'editors' => $editors, 'categories' => $categories, 'formats' => $formats,
            'locations' => $locations, 'status' => $status, 'incompletForm' => $incompletForm
        ]);
    }

    /**
     * ! ADD AUTHOR
     */
    public function addAuthor(): string
    {

        $errors = [];
        if (!empty($_POST['author_name'])) {
            $formProcessing = new FormProcessing();
            $errors = $formProcessing->verifyAndAddAuthor();
        }
        return $this->twig->render('Authors/addAuthor.html.twig', ['errors' => $errors]);
    }

    /**
     * ! ADD EDITOR
     */
    public function addEditor(): string
    {

        $errors = [];
        if (!empty($_POST['editor_name'])) {
            $formProcessing = new FormProcessing();
            $errors = $formProcessing->verifyAndAddEditor();
        }
        return $this->twig->render('Editors/addEditor.html.twig', ['errors' => $errors]);
    }

    /**
     * ! ADD CATEGORY
     */
    public function addCategory(): string
    {

        $errors = [];
        if (!empty($_POST['category_name'])) {
            $formProcessing = new FormProcessing();
            $errors = $formProcessing->verifyAndAddCategory();
        }
        return $this->twig->render('Categories/addCategory.html.twig', ['errors' => $errors]);
    }

    /**
     * ! ADD LOCATION
     */
    public function addLocation(): string
    {

        $errors = [];
        if (!empty($_POST['location_name'])) {
            $formProcessing = new FormProcessing();
            $errors = $formProcessing->verifyAndAddLocation();
        }
        return $this->twig->render('Locations/addLocation.html.twig', ['errors' => $errors]);
    }
}
