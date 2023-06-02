<?php
/**
 * Created by PhpStorm.
 * User: noahheyl
 * Date: 2018-02-01
 * Time: 10:56 AM
 */

namespace falkirks\minereset\util;


use falkirks\minereset\exception\InvalidBlockStringException;
use pocketmine\block\BlockTypeIds;
use pocketmine\item\ItemBlock;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use ReflectionClass;

class BlockStringParser
{
	private static array $blockMap = [];

	private static function ensureMap(): void {
		if (empty(self::$blockMap)) {
			self::$blockMap = (new ReflectionClass(BlockTypeIds::class))->getConstants();
		}
	}

    public static function isValid(string $str): bool {
        try{
            $item = StringToItemParser::getInstance()->parse($str) ?? LegacyStringToItemParser::getInstance()->parse($str);
        }catch(LegacyStringToItemParserException $e){
            // NOOP
        }
        return $item instanceof ItemBlock;
    }

	/**
	 * @param string $str
	 * @return array
	 * @throws InvalidBlockStringException
	 */
	public static function parse(string $str): array {
		self::ensureMap();

		if (is_numeric($str)) {
			return [$str, 0];
		} elseif (isset(self::$blockMap[strtoupper($str)])) {
			return [self::$blockMap[strtoupper($str)], 0];
		}


		$arr = explode(":", $str);
		if (count($arr) === 2 && is_numeric($arr[1])) {
			if (is_numeric($arr[0])) {
				return [$arr[0], $arr[1]];
			} elseif (isset(self::$blockMap[strtoupper($arr[0])])) {
				return [self::$blockMap[strtoupper($arr[0])], $arr[1]];
			}
		}

		throw new InvalidBlockStringException();
	}

}