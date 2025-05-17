<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
       //Etape 1. creation d'un faux client qui doit pointer vers une url
        $client = static::createClient();
        $client->request('GET', '/inscription');

        // Etape 2. Remplir les champs de mon formulaire d'inscription
        $client->submitForm('Valider', [
            'register_user_type_form[email]' => 'paul@exemple.fr',
            'register_user_type_form[plainPassword][first]' => '123456',
            'register_user_type_form[plainPassword][second]' => '123456',
            'register_user_type_form[firstname]' => 'Paul',
            'register_user_type_form[lastname]' => 'Dupont',
        ]);

        // Follow(suivre les redirections)
        $this->assertResponseRedirects('/connexion');
        $client->followRedirect();

        // Etape 3. msg de confirmation de la création du compte
        $this->assertSelectorExists('div:contains("Votre compte est correctement créé, veuillez vous connecter.")');

    }
}
