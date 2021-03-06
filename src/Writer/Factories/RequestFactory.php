<?php

namespace Mobly\LinxMicrovix\Writer\Factories;

use Mobly\LinxMicrovix\Writer\Configuration;
use Mobly\LinxMicrovix\Writer\Entities\CommandParameter;
use Mobly\LinxMicrovix\Writer\Entities\Request;
use Mobly\LinxMicrovix\Writer\Entities\Table;
use Mobly\LinxMicrovix\Writer\Entities\UserAuthentication;

/**
 * Class RequestFactory
 * @package Mobly\LinxMicrovx\Writer\Factories
 */
class RequestFactory
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $requestParameters = [];

    /**
     * @var UserAuthentication
     */
    protected $authenticate;

    /**
     * RequestFactory constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->setRequestParameters();
        $this->setAuthenticationParameter();
    }

    protected function setRequestParameters()
    {
        $portal = new CommandParameter();
        $portal->setName('IdPortal');
        $portal->setValue($this->configuration->getIdPortal());

        $key = new CommandParameter();
        $key->setName('chave');
        $key->setValue($this->configuration->getKeyPortal());

        $document = new CommandParameter();
        $document->setName('cnpjEmp');
        $document->setValue($this->configuration->getCnpjEmp());

        $this->requestParameters[] = $portal;
        $this->requestParameters[] = $key;
        $this->requestParameters[] = $document;
    }

    protected function setAuthenticationParameter()
    {
        $this->authenticate = new UserAuthentication();
        $this->authenticate->setPassword($this->configuration->getPassword());
        $this->authenticate->setUser($this->configuration->getUser());
    }

    /**
     * @param $tableName
     * @param array $data
     * @return Request
     */
    public function build($tableName, array $data)
    {
        $table = new Table();
        $table->setCommand($tableName);

        $this->setData($table, $data);

        $request = new Request($this->authenticate, $this->requestParameters);
        $request->setTable($table);

        return $request;
    }

    /**
     * @param Table $table
     * @param array $data
     */
    protected function setData(Table $table, array $data)
    {
        $records = [];

        foreach ($data as $line) {
            $record = new Table\Record();
            $columns = [];
            foreach ($line as $key => $value) {
                $column = new CommandParameter();
                $column->setName($key);
                $column->setValue($value);

                $columns[] = $column;
            }

            $record->setColumn($columns);
            $records[] = $record;
        }

        $table->setData($records);
    }
}