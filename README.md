Hangman
========

Install instructions
-----------

    1. git clone https://github.com/houdtbaar/Hangman.git
    2. cd Hangman
    3. composer install
    4. php app/console doctrine:schema:update --force
    5. php app/console doctrine:fixtures:load
    6. Start build-in webserver:  php app/console server:run
    7. phpunit -c app src/Houdtbaar/HangmanBundle
    
Good luck playing!