pipeline {
  agent any
  stages {
    stage('Deploy') {
      steps {
        sh 'bin/console deploy prod'
      }
    }
  }
}