<?php 

namespace App\Classe; 
use Mailjet\Client;
use Mailjet\Resources;

class Mail 
{
    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        // Recup du template 
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);

        // Recup les variables facultatives
        if($vars) {
            foreach($vars as $key=>$var) {
               $content = str_replace('{'.$key.'}', $var, $content );
            }
        }
        
        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);
        
        $body = [
    'Messages' => [
        [
            'From' => [
                'Email' => "nathanaelkalenga2@gmail.com",
                'Name' => "La boutique française"
            ],
            'To' => [
                [
                    'Email' => $to_email,
                    'Name' => $to_name
                ]
            ],
            'TemplateID' => 7041888,
             'TemplateLanguage' => true,
            'Subject' => $subject,
            'variables' => [
               'content' => $content
            ],
            
        ]
    ]
];
   
    // Envoi de l'email 
    $response = $mj->post(Resources::$Email, ['body' => $body]);

   

    }
}