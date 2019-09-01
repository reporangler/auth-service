<?php
namespace App\Services;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DatabaseAuthenticator
{
    public function auth($username, $password)
    {
        switch($username){
            case "chris":
                return [
                    'id' => 1,
                    'username' => $username,
                    'groups' => [
                        $username,
                        'companyA',
                        'companyB',
                    ],
                ];
                break;

            case 'alex':
                return [
                    'id' => 2,
                    'username' => $username,
                    'groups' => [
                        $username,
                        'companyA',
                        'companyC',
                    ]
                ];
                break;
        }

        throw new NotFoundHttpException("This user does not exist");
    }
}
