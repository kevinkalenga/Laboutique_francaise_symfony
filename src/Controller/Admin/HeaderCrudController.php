<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

      public function configureFields(string $pageName): iterable
    {

        $required = true;

        if ($pageName == 'edit') {
            $required = false;
        }



        return [

            TextField::new('title', 'Titre'),
            TextareaField::new('content', 'Contenu'),
            TextField::new('buttonTitle', 'Titre du bouton'),
            TextField::new('buttonLink', 'URL du bouton'),
            ImageField::new('illustration')
                ->setLabel('Image de fond du header')
                ->setHelp('Image de fond du header en JPG')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
                ->setBasePath('/uploads')
                ->setUploadDir('/public/uploads')
                ->setRequired($required),
        ];
    }

    
    
    
    
    
    
    
    
    
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
