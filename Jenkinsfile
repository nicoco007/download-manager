pipeline {
  agent any
  stages {
    stage('Deploy') {
      steps {
        sh 'composer install'
        sh 'bin/console deploy prod'
      }
    }
  }
}