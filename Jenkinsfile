pipeline {
  agent any
  stages {
    stage('Deploy') {
      steps {
        sh 'composer install'
        sh 'php bin/console deploy prod'
      }
    }
  }
}