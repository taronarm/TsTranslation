<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class TsDbMessageSource extends CMessageSource {

    const CACHE_KEY_PREFIX = 'Yii.TsDbMessageSource.';

    public $connectionID = 'db';
    public $sourceMessageTable = 'tsy_source_messages';
    public $translatedMessageTable = 'tsy_translated_messages';
    public $cachingDuration = 0;
    public $cacheID = false; //'cache';

    /**
     * message which must shows if message translation is empty
     * @var string or `false/null`
     */
    public $notTranslatedMessage = null;

    /**
     * enable/disable default language message shown if current language message translation is empty,
     * if default language translation is always empty shows `$this->notTranslatedMessage`
     * @var bool
     */
    public $ifNotTranslatedShowDefault = true;
    private $_db;

    public function translate($category, $message, $language = null) {
        $returnedValue = null;
        if (is_object($category)) {
            if ($category->isNewRecord || $category->getPrimaryKey() === null) {
                return null;
            }
            if ($category->hasAttribute($message) || property_exists($category, $message)) {
                $category = '#.' . get_class($category) . '-' . $message . '.' . $category->getPrimaryKey();
                $message = null;
            } else {
                throw new TsTranslationException('The model ' . get_class($category) . ' have not attribute ' . $message);
            }
        } elseif ($category === null) {
            $category = '';
            if (Yii::app()->controller->module !== null) {
                $category .= Yii::app()->controller->module->id . '.';
            } else {
                $category .= 'root.';
            }
            $category .= Yii::app()->controller->id . '.' . Yii::app()->controller->action->id;
        }
        if ($language === null)
            $language = Yii::app()->getLanguage();
        if ($this->forceTranslation || $language !== $this->getLanguage() || strpos($category, '#.') === 0) {
            $returnedValue = $this->translateMessage($category, $message, $language);
        } else {
            $returnedValue = $message;
        }
        if ($this->ifNotTranslatedShowDefault && $language !== TsTranslationComponent::getDefaultLanguage('code2') && is_null($returnedValue)) {
            $returnedValue = $this->translate($category, $message, TsTranslationComponent::getDefaultLanguage('code2'));
        }

        return !is_null($returnedValue) ? $returnedValue : $this->notTranslatedMessage;
    }

    public function getDbConnection() {

        if ($this->_db === null) {
            $this->_db = Yii::app()->getComponent($this->connectionID);
            if (!$this->_db instanceof CDbConnection)
                throw new CException(Yii::t('yii', 'CDbMessageSource.connectionID is invalid. Please make sure "{id}" refers to a valid database application component.', array('{id}' => $this->connectionID)));
        }
        return $this->_db;
    }

    protected function loadMessages($category, $language) {

        if ($this->cachingDuration > 0 && $this->cacheID !== false && ($cache = Yii::app()->getComponent($this->cacheID)) !== null) {
            $key = self::CACHE_KEY_PREFIX . '.messages.' . $category . '.' . $language;
            if (($data = $cache->get($key)) !== false)
                return unserialize($data);
        }

        $messages = $this->loadMessagesFromDb($category, $language);

        if (isset($cache))
            $cache->set($key, serialize($messages), $this->cachingDuration);

        return $messages;
    }

    protected function loadMessagesFromDb($category, $language) {

        $sql = <<<EOD
SELECT t1.message AS message, t2.translation AS translation
FROM {$this->sourceMessageTable} t1, {$this->translatedMessageTable} t2
WHERE t1.id=t2.id AND t1.category=:category AND t2.language=:language
EOD;
        $command = $this->getDbConnection()->createCommand($sql);
        $command->bindValue(':category', $category);
        $command->bindValue(':language', $language);
        $messages = array();
        foreach ($command->queryAll() as $row) {
            $messages[$row['message']] = $row['translation'];
        }

        return $messages;
    }

}
