<?php

namespace JasperPHP;

class JasperPHP
{
    protected $executable = 'jasperstarter'; //executable jasperstarter
	protected $lang;
    protected $path_executable;
    protected $the_command;
    protected $windows = false;

    protected $formats = array('pdf', 'rtf', 'xls', 'xlsx', 'docx', 'odt', 'ods', 'pptx', 'csv', 'html', 'xhtml', 'xml', 'jrprint');
    protected $resource_directory; //Path to report resource dir or jar file

    function __construct($resource_dir = false, $lang = "pt_BR.UTF-8" )
    {
		$this->lang = $lang;
        $this->path_executable = __DIR__ . '/../JasperStarter/bin'; //Path to executable
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            $this->windows = true;

        if (!$resource_dir) {
            $this->resource_directory = __DIR__ . '/../../../vendor/copam/jasperphp/src/JasperStarter/bin';
        } else {
            if (!file_exists($resource_dir))
                throw new \Exception('Diretório não encontrado!', 1);

            $this->resource_directory = $resource_dir;
        }

    }

    public static function __callStatic($method, $parameters)
    {
        // Create a new instance of the called class, in this case it is Post
        $model = get_called_class();

        // Call the requested method on the newly created object
        return call_user_func_array(array(new $model, $method), $parameters);
    }

    public function compile($input_file, $output_file = false)
    {
        if (is_null($input_file) || empty($input_file))
            throw new \Exception('Arquivo não encontrado!', 1);

        $command = ($this->windows) ? $this->executable :  'LANG=' . $this->lang . ' ./' . $this->executable;

        $command .= ' compile ';

        $command .= "\"$input_file\"";

        if ($output_file !== false)
            $command .= ' -o ' . "\"$output_file\"";

        $this->the_command = $command;

        return $this;
    }

    public function process($input_file, $output_file = false, $format = ['pdf'], $parameters = [], $db_connection = [], $locale = 'pt_BR')
    {
        if (is_null($input_file) || empty($input_file))
            throw new \Exception('Arquivo não encontrado!', 1);

        if (is_array($format)) {
            foreach ($format as $key) {
                if (!in_array($key, $this->formats))
                    throw new \Exception('Formato inválido!', 1);
            }
        } else {
            if (!in_array($format, $this->formats))
                throw new \Exception('Formato inválido!', 1);
        }

        $command = ($this->windows) ? $this->executable :  'LANG=' . $this->lang . ' ./' . $this->executable;
		
        $command .= ($locale) ? " --locale $locale" : '';

        $command .= ' process ';

        $command .= "\"$input_file\"";

        if ($output_file !== false) {
            $command .= ' -o ' . "\"$output_file\"";
        }

        if (is_array($format)) {
            $command .= ' -f ' . join(' ', $format);
        } else {
            $command .= ' -f ' . $format;
        }

        if (count($parameters) > 0) {
            $command .= ' -P ';

            foreach ($parameters as $key => $value) {
                $param = $key . '="' . $value . '" ';
                $command .= " " . $param . " ";
            }

        }

        if (count($db_connection) > 0) {
            $command .= ' -t ' . $db_connection['driver'];

            if (isset($db_connection['username'])) {
                $command .= " -u " . $db_connection['username'];
            }

            if (isset($db_connection['password']) && !empty($db_connection['password'])) {
                $command .= ' -p ' . $db_connection['password'];
            }

            if (isset($db_connection['host']) && !empty($db_connection['host'])) {
                $command .= ' -H ' . $db_connection['host'];
            }

            if (isset($db_connection['database']) && !empty($db_connection['database'])) {
                $command .= ' -n ' . $db_connection['database'];
            }

            if (isset($db_connection['port']) && !empty($db_connection['port'])) {
                $command .= ' --db-port ' . $db_connection['port'];
            }

            if (isset($db_connection['jdbc_driver']) && !empty($db_connection['jdbc_driver'])) {
                $command .= ' --db-driver ' . $db_connection['jdbc_driver'];
            }

            if (isset($db_connection['jdbc_url']) && !empty($db_connection['jdbc_url'])) {
                $command .= ' --db-url ' . $db_connection['jdbc_url'];
            }

            if (isset($db_connection['jdbc_dir']) && !empty($db_connection['jdbc_dir'])) {
                $command .= ' --jdbc-dir ' . $db_connection['jdbc_dir'];
            }

            if (isset($db_connection['db_sid']) && !empty($db_connection['db_sid'])) {
                $command .= ' --db-sid ' . $db_connection['db_sid'];
            }

            if (isset($db_connection['xml_xpath'])) {
                $command .= ' --xml-xpath ' . $db_connection['xml_xpath'];
            }

            if (isset($db_connection['data_file'])) {
                $command .= ' --data-file ' . $db_connection['data_file'];
            }

            if (isset($db_connection['json_query'])) {
                $command .= ' --json-query ' . $db_connection['json_query'];
            }
        }

        $this->the_command = $command;
        return $this;
    }

    public function list_parameters($input_file)
    {
        if (is_null($input_file) || empty($input_file))
            throw new \Exception('Arquivo não encontrado!', 1);

        $command = ($this->windows) ? $this->executable :  'LANG=' . $this->lang . ' ./' . $this->executable;

        $command .= ' list_parameters ';

        $command .= "\"$input_file\"";

        $this->the_command = $command;

        return $this;
    }

    public function output()
    {
        return $this->the_command;
    }

    public function execute($run_as_user = false)
    {

        if ($run_as_user !== false && strlen($run_as_user > 0) && !$this->windows) {
            $this->the_command = 'su -u ' . $run_as_user . " -c \"" . $this->the_command . "\"";
        }

        $output = [];
        $return_var = 0;

        if (is_dir($this->path_executable)) {
            chdir($this->path_executable);
            exec($this->the_command, $output, $return_var);
        } else {
            throw new \Exception('Diretório inválido.', 1);
        }

        if ($return_var != 0)
            throw new \Exception('Seu relatório tem um erro e não pode ser processado! Execute a função `output();` e rode o comando gerado no seu console/terminal.', 1);

        return $output;
    }
}