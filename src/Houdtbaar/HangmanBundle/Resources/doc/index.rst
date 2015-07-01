
1. git clone
2. composer install
3. php app/console doctrine:schema:update --force
4. php app/console doctrine:mongodb:fixtures:load
5. phpunit -c app src/Houdtbaar/HangmanBundle
