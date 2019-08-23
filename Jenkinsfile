pipeline {
  agent any
  stages {
    stage('Deploy') {
      steps {
        sh 'ssh-add ~/.ssh/id_ed25519'
        sh 'composer install'
        sh 'APP_ENV=test php bin/console deploy prod'
      }
    }
  }
}