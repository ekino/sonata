# Can lock and unlock maintenance by site

## Composer

Run this command on your project:

```bash
composer require lexik/maintenance-bundle
```

## Configuration

Replace default config by the following configuration to your lexik_maintenance.yaml.

```yaml
lexik_maintenance:
    driver:
        ttl:   300
        class: 'Lexik\Bundle\MaintenanceBundle\Drivers\DatabaseDriver'
```

## Entity

Create or update a Site entity based of `Sonata\PageBundle\Entity\BaseSite` and add new field named `$maintenanceTtl`, as this example:

```php
<?php

/*
 * This file is part of the Ekino project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Sonata;

use Doctrine\ORM\Mapping as ORM;
use Sonata\PageBundle\Entity\BaseSite;

/**
 * @ORM\Entity
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class Site extends BaseSite
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $maintenanceTtl;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getMaintenanceTtl(): ?\DateTime
    {
        return $this->maintenanceTtl;
    }

    /**
     * @param \DateTime|null $maintenanceTtl
     *
     * @return Site
     */
    public function setMaintenanceTtl(?\DateTime $maintenanceTtl): Site
    {
        $this->maintenanceTtl = $maintenanceTtl;

        return $this;
    }
}
```

And generate new migration with `doctrine:migrations:diff`, as this example of migration on PostgreSQL:

```php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191126094211 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE site ADD maintenance_ttl TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE site DROP maintenance_ttl');
    }
}
```

And run migration with `doctrine:migrations:migrate`

## Usage

Now, you can lock and unlock all sites provided by your Sonata application with:

`bin/console lexik:maintenance:lock` and `bin/console lexik:maintenance:unlock`

Or you can lock and unlock specificly a site by this `id` with:

`bin/console lexik:maintenance:lock --site-id=1` and `bin/console lexik:maintenance:unlock --site-id=1`
