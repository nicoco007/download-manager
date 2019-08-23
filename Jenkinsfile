pipeline {
  agent any
  stages {
    stage('Deploy') {
      steps {
        sh 'composer install'

        sshagent (credentials: ['deploy']) {
          sh 'APP_ENV=test php bin/console deploy prod'
        }
      }
    }
  }
}
