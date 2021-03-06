<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueDist\Model\SubFolder\Branch;
use VerteXVaaR\BlueDist\Model\SubFolder\Leaf;
use VerteXVaaR\BlueDist\Model\SubFolder\Tree;
use VerteXVaaR\BlueSprints\Mvc\AbstractController;

class Welcome extends AbstractController
{
    protected function index(): void
    {
        $this->templateRenderer->setVariable(
            'strings',
            [
                'foo',
                'bar',
                'baz',
            ]
        );
    }

    protected function listFruits(): void
    {
        $this->templateRenderer->setVariable('fruits', Fruit::findAll());
    }

    protected function createDemoFruits(): void
    {
        $fruitsData = [
            [
                'color' => 'red',
                'name' => 'Apple',
            ],
            [
                'color' => 'yellow',
                'name' => 'Banana',
            ],
            [
                'color' => 'black',
                'name' => 'Blackberry',
            ],
            [
                'color' => 'red',
                'name' => 'Strawberry',
            ],
        ];
        foreach ($fruitsData as $fruitData) {
            $fruit = new Fruit();
            $fruit->setColor($fruitData['color']);
            $fruit->setName($fruitData['name']);
            $fruit->save();
        }
        $this->redirect('listFruits');
    }

    protected function createFruit(): void
    {
        $arguments = $this->request->getParsedBody();
        if (isset($arguments['name'], $arguments['color'])) {
            $fruit = new Fruit();
            $fruit->setColor($arguments['color']);
            $fruit->setName($arguments['name']);
            $fruit->save();
        }
        $this->redirect('listFruits');
    }

    protected function editFruit(): void
    {
        $fruit = Fruit::findByUuid($this->request->getQueryParams()['fruit']);
        $this->templateRenderer->setVariable('fruit', $fruit);
    }

    protected function updateFruit(): void
    {
        $arguments = $this->request->getParsedBody();
        if (isset($arguments['uuid'], $arguments['name'], $arguments['color'])) {
            $fruit = Fruit::findByUuid($arguments['uuid']);
            $fruit->setName($arguments['name']);
            $fruit->setColor($arguments['color']);
            $fruit->save();
        }
        $this->redirect('listFruits');
    }

    protected function createTree(): void
    {
        $arguments = $this->request->getParsedBody();
        $tree = new Tree();
        $tree->setGenus($arguments['genus']);
        $tree->save();
        $this->templateRenderer->setVariable('tree', $tree);
        $this->templateRenderer->setVariable('branches', range(1, $arguments['numberOfBranches']));
    }

    protected function newTree(): void
    {
    }

    protected function growBranches(): void
    {
        $arguments = $this->request->getParsedBody();
        $tree = Tree::findByUuid($arguments['tree']);
        $branches = [];
        foreach ($arguments['branches'] as $data) {
            $branch = new Branch();
            $branch->setLength((int)$data['length']);
            $branches[] = $branch;
        }
        $tree->setBranches($branches);
        $tree->save();
        $this->redirect('applyLeaves?tree=' . $tree->getUuid());
    }

    protected function applyLeaves(): void
    {
        $arguments = $this->request->getQueryParams();
        $this->templateRenderer->setVariable(
            'tree',
            Tree::findByUuid($arguments['tree'])
        );
    }

    protected function addLeaf(): void
    {
        $arguments = $this->request->getParsedBody();
        $tree = Tree::findByUuid($arguments['tree']);
        $branch = $tree->getBranches()[$arguments['branch']];
        $leaves = $branch->getLeaves();
        $leaves[] = new Leaf(count($leaves) + 1);
        $branch->setLeaves($leaves);
        $tree->save();
        $this->redirect('applyLeaves?tree=' . $tree->getUuid());
    }

    protected function deleteFruit(): void
    {
        $fruit = Fruit::findByUuid($this->request->getParsedBody()['fruit']);
        $fruit->delete();
        $this->redirect('listFruits');
    }
}
