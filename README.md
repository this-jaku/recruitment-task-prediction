# recruitment-task-prediction

Demo application for recruitment task.
Description of the task available in `resources/task.pdf`

## setup
  - clone project
  - install Symfony CLI https://symfony.com/download
  - add MySql databases:
    - recruitment_task_prediction
    - recruitment_task_prediction_test (used for functional tests)
  - edit database user/password/host if need. Default:
    -  .env `DATABASE_URL=mysql://admin:password@127.0.0.1:3306/recruitment_task_prediction?serverVersion=8.0`
    -  .env.local `DATABASE_URL=mysql://admin:password@127.0.0.1:3306/recruitment_task_prediction_test?serverVersion=8.0`
  - in project folder run `composer install`  
  - run migrations `bin/console doctrine:migrations:migrate && bin/console doctrine:migrations:migrate --env=test`
  - run server `symfony server:start`
  - import Postman collection from `resources/recruitment-task-prediction.postman_collection.json`
 