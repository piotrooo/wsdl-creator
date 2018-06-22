pipeline {
  agent any
  stages {
    stage('composer') {
      steps {
        sh 'composer install'
      }
    }
    stage('tests') {
      steps {
        sh 'ant phpunit'
      }
    }
  }
}