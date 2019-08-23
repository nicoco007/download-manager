pipeline {
  agent any
  stages {
    stage('Deploy') {
      steps {
        sh 'composer install'
        sh 'APP_ENV=test php bin/console deploy prod'
      }
    }
  }
}