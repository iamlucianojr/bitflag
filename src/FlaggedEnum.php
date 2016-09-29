<?php

namespace Lucianojr\BitFlag;

abstract class FlaggedEnum extends Enum
{
    const NONE = 0;
    private static $masks = [];
    protected $flags;

    /**
     * {@inheritdoc}
     */
    public static function isAcceptableValue($value)
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type "integer", "%s" given.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }
        if ($value === self::NONE) {
            return true;
        }
        return $value === ($value & static::getBitmask());
    }

    /**
     * {@inheritdoc}
     */
    public static function getReadableForInArray($value)
    {
        if (! static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value);
        }
        if ($value === self::NONE) {
            return static::getReadableForNone();
        }
        $humanRepresentations = static::getReadables();
        if (isset($humanRepresentations[$value])) {
            return $humanRepresentations[$value];
        }
        $parts = array();
        foreach ($humanRepresentations as $flag => $readableValue) {
            if ($flag === ($flag & $value)) {
                $parts[] = $readableValue;
            }
        }
        return $parts;
    }

    /**
     * {@inheritdoc}
     */
    public static function getReadableFor($value, $separator = '; ')
    {
        return implode($separator, static::getReadableForInArray($value));
    }

    /**
     * Gets the human representation for the none value.
     *
     * @return string
     */
    protected static function getReadableForNone()
    {
        return 'none';
    }
    /**
     * Gets an integer value of the possible flags for enumeration.
     *
     * @return int
     *
     * @throws \UnexpectedValueException
     */
    protected static function getBitMask()
    {
        $enumType = get_called_class();
        if (!isset(self::$masks[$enumType])) {
            $mask = 0;
            foreach (static::getPossibleValues() as $flag) {
                if ($flag < 1 || ($flag > 1 && ($flag % 2) !== 0)) {
                    throw new \UnexpectedValueException(sprintf(
                        "Possible value (%d) of the enumeration is not the bit flag.",
                        $flag
                    ));
                }
                $mask |= $flag;
            }
            self::$masks[$enumType] = $mask;
        }

        return self::$masks[$enumType];
    }
    /**
     * Gets the bitmask of possible values.
     *
     * @return int
     *
     * @throws \UnexpectedValueException
     *
     * @deprecated
     */
    protected static function getMaskOfPossibleValues()
    {
        $mask = 0;
        foreach (static::getPossibleValues() as $flag) {
            if ($flag > 1 && ($flag % 2) !== 0) {
                throw new \UnexpectedValueException(sprintf(
                    'Possible value (%d) of the enumeration is not the bit flag.',
                    $flag
                ));
            }
            $mask |= $flag;
        }
        return $mask;
    }
    /**
     * {@inheritdoc}
     */
    public function getReadable($separator = '; ')
    {
        return static::getReadableFor($this->getValue(), $separator);
    }

    /**
     * Gets an array of bit flags of the value.
     *
     * @return array
     */
    public function getFlags()
    {
        if ($this->flags === null) {
            $this->flags = array();
            foreach (static::getPossibleValues() as $flag) {
                if ($this->hasFlag($flag)) {
                    $this->flags[] = $flag;
                }
            }
        }
        return $this->flags;
    }

    /**
     * Determines whether the specified flag is set in a numeric value.
     *
     * @param int $bitFlag The bit flag or bit flags.
     * @return bool True if the bit flag or bit flags are also set in the current instance; otherwise, false
     */
    public function hasFlag($bitFlag)
    {
        if ($bitFlag >= 1) {
            return $bitFlag === ($bitFlag & $this->value);
        }
        return false;
    }

    /**
     * Adds a bitmask to the value of this instance.
     *
     * Returns a new instance of this enumeration type.
     *
     * @param int $flags The bit flag or bit flags
     * @return EnumInterface
     * @throws InvalidEnumArgumentException When $flags is not acceptable for this enumeration type
     */
    public function addFlags($flags)
    {
        if (!static::isAcceptableValue($flags)) {
            throw new InvalidEnumArgumentException($flags);
        }
        return static::create($this->value | $flags);
    }

    /**
     * Removes a bitmask from the value of this instance.
     *
     * Returns a new instance of this enumeration type.
     *
     * @param int $flags The bit flag or bit flags
     * @return EnumInterface
     * @throws InvalidEnumArgumentException When $flags is not acceptable for this enumeration type
     */
    public function removeFlags($flags)
    {
        if (!static::isAcceptableValue($flags)) {
            throw new InvalidEnumArgumentException($flags);
        }
        return static::create($this->value & ~$flags);
    }
}
