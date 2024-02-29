<?php

namespace PHPSTORM_META {

    override(\Symfony\Component\DependencyInjection\Container::get(), type(0));
    override(\VerteXVaaR\BlueSprints\Mvcr\Repository\Repository::findByIdentifier(), type(0));
    override(\VerteXVaaR\BlueSprints\Mvcr\Repository\Repository::findAll(), map(['' => '@[]']));
}
