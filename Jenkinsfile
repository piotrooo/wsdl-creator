pipeline {
  agent any
  stages {
    stage('composer') {
      steps {
        script {
          withEnv( ["ANT_HOME=ant"] ) {
            sh '$ANT_HOME/bin/ant tests'
          }
        }

      }
    }
  }
}