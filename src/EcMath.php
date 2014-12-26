<?php

namespace Mdanter\Ecc;

/**
 * Class EcMath
 * @package Mdanter\Ecc
 * @author Thomas Kerin
 */
class EcMath
{
    /**
     * @var int|PointInterface
     */
    private $data;

    /**
     * @var string
     */
    private $dataType;

    /**
     * @var MathAdapter
     */
    private $math;

    /**
     * @var GeneratorPoint
     */
    private $generator;

    /**
     * @param int|PointInterface $input
     * @param GeneratorPoint $g
     * @param MathAdapter $math
     */
    public function __construct($input, GeneratorPoint $g, MathAdapter $math)
    {
        $this->dataType  = $this->identify($input);
        $this->data      = $input;
        $this->math      = $math;
        $this->generator = $g;
        return $this;
    }

    /**
     * @param $input
     * @return string
     */
    public function identify($input)
    {
        if ($input instanceof PointInterface) {
            return 'point';
        } else if (is_numeric($input)) {
            return 'int';
        } else {
            throw new \LogicException('Must provide a point or integer');
        }
    }

    /**
     * @return GeneratorPoint
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * This function handles cases where the two types are not identical (but
     * the result will always be a point).
     *
     * Since the current data could be either a point or an int, (with the operand taking
     * the opposite type), this function ensures that the callable $handler ALWAYS receives
     * it's arguments in the order of (Point, int).
     *
     * @param int|PointInterface $operand
     * @param callable $handler
     * @return $this
     */
    private function handleOppositeTypes($operand, callable $handler)
    {
        $data =  $this->data;
        if ($this->dataType == 'int') {
            list ($data, $operand) = array($operand, $data);
        }

        $this->dataType = 'point';

        // handler (currentData, operand):
        //   <operation> (point, int) -> point
        $this->data = $handler($data, $operand);
        return $this;
    }

    /**
     * @param $addend
     * @return $this
     */
    public function add($addend)
    {
        $type = $this->identify($addend);

        if ($this->dataType == 'point' && $type == 'point') {
            $this->data = $this->data->add($addend);
            return $this;
        }

        if ($this->dataType == 'int' && $type == 'int') {
            $this->data = $this->math->add($this->data, $addend);
            return $this;
        }

        $this->handleOppositeTypes($addend,
            function (PointInterface $data, $addendInt) {
                $point = $this->getGenerator()->mul($addendInt);
                return $data->add($point);
            }
        );

        return $this;
    }

    /**
     * @param $multiplicand
     * @return $this
     */
    public function mul($multiplicand)
    {
        $type = $this->identify($multiplicand);

        if ($this->dataType == 'point' && $type == 'point') {
            throw new \RuntimeException('Cannot multiply two points together');
        }

        if ($this->dataType == 'int' && $type == 'int') {
            $this->data = $this->math->mul($this->data, $multiplicand);
            return $this;
        }

        $this->handleOppositeTypes($multiplicand,
            function (PointInterface $data, $multiplicandInt) {
                return $data->mul($multiplicandInt);
            }
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function getDouble()
    {
        $data = $this->data;

        if ($this->dataType == 'point') {
            $this->data = $data->getDouble();
            return $this;
        }

        if ($this->dataType == 'int') {
            $this->data = $this->math->mul($data, '2');
            return $this;
        }

        return $this;
    }

    /**
     * @param $n
     * @return $this
     */
    public function mod($n)
    {
        if ($this->identify($n) !== 'int') {
            throw new \LogicException('Parameter for mod() must be an integer');
        }

        if ($this->dataType == 'int') {
            $this->data = $this->math->mod($this->data, $n);
        }

        return $this;
    }

    /**
     * @param $input
     * @return int|string
     */
    public function cmp($input)
    {
        $type = $this->identify($input);

        if ($this->dataType == 'point' && $type == 'point') {
            return $this->data->cmp($input);
        }

        if ($this->dataType == 'int' && $type == 'int') {
            return $this->math->cmp($this->data, $input);
        }

        $data = $this->data;

        // Data should become the point
        if ($this->dataType == 'int') {
            list ($data, $input) = array($input, $data);
        }

        $point = $this->getGenerator()->mul($input);
        return $data->cmp($point);
    }

    /**
     * Calculate the result of this computation, and update subject with the
     * result of the calculation to avoid recomputing it.
     *
     * @return int|PointInterface
     */
    public function result()
    {
        $data = $this->data;
        return $data;
    }

    /**
     * @return int|GeneratorPoint|Infinity|PointInterface
     */
    public function getPoint()
    {
        $point = $this->data;

        if ($this->dataType == 'int') {
            $point = $this->getGenerator()->mul($point);
        }

        return $point;

    }
};