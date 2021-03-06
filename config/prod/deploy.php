<?php

use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class extends DefaultDeployer
{
    public function configure()
    {
        return $this->getConfigBuilder()
            ->server('deploy@ns509500.gnyra.com')
            ->deployDir('/var/www/nicoco007.com/downloads-deploy')
            ->repositoryUrl('git@github.com:nicoco007/download-manager')
            ->repositoryBranch('master')
            ->sharedFilesAndDirs(['var/log', 'config/packages/prod/doctrine.yaml', 'uploads'])
            ->composerInstallFlags('--prefer-dist --no-interaction --no-dev')
            ->remoteComposerBinaryPath('composer');
    }

    public function beforePublishing()
    {
        $this->log('Installing dependencies');
        $this->runRemote('yarn install');

        $this->log('Running Webpack Encore');
        $this->runRemote('yarn run encore production');

        $this->log('Applying migrations');
        $this->runRemote('{{ console_bin }} doctrine:migrations:migrate --no-interaction');

        $this->log('Warmup cache');
        $this->runRemote('{{ console_bin }} cache:warmup');
    }
};
