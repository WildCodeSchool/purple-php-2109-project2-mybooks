<?php

namespace App\Controller;

use App\Model\BookManager;
use App\Model\LocationManager;
use App\Model\AuthorManager;
use App\Model\EditorManager;
use App\Model\CategoryManager;
use App\Model\FormatManager;
use App\Model\FormProcessing;
use App\Model\StatusManager;
use App\Model\VerificationProcess;

class BookController extends AbstractController
{
    public function addBook(): string
    {
        session_start();
        $_SESSION["location"] = "add";
        /**
         * ! GET ELEMENT FOR LIST IN FORM
         */
        $authorsManager = new AuthorManager();
        $authors = $authorsManager->selectAll('name');

        $editorsManager = new EditorManager();
        $editors = $editorsManager->selectAll('name');

        $categoriesManager = new CategoryManager();
        $categories = $categoriesManager->selectAll('name');

        $formatsManager = new FormatManager();
        $formats = $formatsManager->selectAll('id');

        $locationsManager = new LocationManager();
        $locations = $locationsManager->selectAll('name');

        $statusManager = new StatusManager();
        $status = $statusManager->selectAll('id');
        /**
         * ! PUT THE BOOK IN DBB
         */
        $formProcessing = new FormProcessing();
        $errors = [];

        $errors = $formProcessing->verifyEmptyPost();

        if (empty($errors) && !empty($_FILES['avatar'])) {
            $path = $formProcessing->coverPage();
            $formProcessing->addBooktoDB($path);
            header('Location: /');
        }

        return $this->twig->render('Books/addBook.html.twig', [
            'authors' => $authors, 'editors' => $editors, 'categories' => $categories, 'formats' => $formats,
            'locations' => $locations, 'status' => $status, 'errors' => $errors
        ]);
    }

    /**
     * ! ADD AUTHOR
     */
    public function addAuthor(): string
    {
        session_start();
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
        session_start();
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
        session_start();
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
        session_start();
        $errors = [];
        if (!empty($_POST['location_name'])) {
            $formProcessing = new FormProcessing();
            $errors = $formProcessing->verifyAndAddLocation();
        }
        return $this->twig->render('Locations/addLocation.html.twig', ['errors' => $errors]);
    }

    /**
     * ! EDIT BOOK
     */
    public function edit(int $id)
    {
        session_start();
        $_SESSION["location"] = "edit";
        $_SESSION["book"] = $_GET["id"];

        $bookManager = new BookManager();

        $book = $bookManager->selectOneById($id);

        $authorManager = new AuthorManager();
        $authors = $authorManager->selectAll();

        $editorManager = new EditorManager();
        $editors = $editorManager->selectAll();

        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();

        $formatManager = new FormatManager();
        $formats = $formatManager->selectAll();

        $locationManager = new LocationManager();
        $locations = $locationManager->selectAll();

        $statusManager = new StatusManager();
        $status = $statusManager->selectAll();
        $errors = [];
        if (!empty($_POST)) {
            $verification  = new VerificationProcess();
            $errors = $verification->TestInputVerification();
        }
        return $this->twig->render('Books/edit.html.twig', [
            'errors' => $errors,
            'book' => $book,
            'authors' => $authors,
            'editors' => $editors,
            'categories' => $categories,
            'formats' => $formats,
            'locations' => $locations,
            'status' => $status
        ]);
    }

    /**
     * ! GET ELEMENT FOR RECAPBOOK
     */
    public function book()
    {
        $bookManager = new BookManager();
        $booksId = $bookManager->selectAllBookId();

        if (in_array($_GET['id'], array_column($booksId, 'id'))) {
            $book = $bookManager->selectOneByIdWithForeignKeys($_GET['id']);
            return $this->twig->render('Books/bookRecap.html.twig', ['book' => $book]);
        } else {
            header('Location: /');
        }
    }

    /**
     * ! DELETE BOOK BY ID
     */
    public function deleteBook(): void
    {
        $bookManager = new BookManager();
        $booksId = $bookManager->selectAllBookId();

        if (in_array($_GET['id'], array_column($booksId, 'id'))) {
            $bookManager = new BookManager();
            $bookManager->delete($_GET['id']);
            header('Location: /');
        } else {
            echo "error";
        }
    }

    /**
     * ! DAHSBOARD
     */
    public function dashboard()
    {
        $bookManager = new BookManager();

        $authorManager = new authorManager();
        $authors = $authorManager->selectAll();

        $editorManager = new EditorManager();
        $editors = $editorManager->selectAll();

        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();

        $formatManager = new FormatManager();
        $formats = $formatManager->selectAll();

        $locationManager = new LocationManager();
        $locations = $locationManager->selectAll();

        $statusManager = new StatusManager();
        $status = $statusManager->selectAll();
        $form = new FormProcessing();
        $sort = $form->verifyGetToSort();

        if (
            !empty($_GET['author_id'])
            || !empty($_GET['editor_id'])
            || !empty($_GET['category_id'])
            || !empty($_GET['format_id'])
            || !empty($_GET['location_id'])
            || !empty($_GET['status_id'])
            || !empty($_GET['sort'])
        ) {
            $items = $form->verifyGetToFilter();
            $books = $bookManager->bookFilterAll($items, $sort);
        } else {
            $books = $bookManager->selectAllCompleteOrdered($sort);
        }

        return $this->twig->render('Dashboard/index.html.twig', [
            'authors' => $authors,
            'editors' => $editors,
            'categories' => $categories,
            'formats' => $formats,
            'locations' => $locations,
            'status' => $status,
            'books' => $books,
        ]);
    }

    /**
     * ! SEARCH_BAR
     */
    public function searchbar(string $title = "")
    {
        $bookManager = new BookManager();
        $books = $bookManager->selectByUnCompleteTitle($title);
        return $this->twig->render('Dashboard/_books.html.twig', ['books' => $books,]);
    }
}
