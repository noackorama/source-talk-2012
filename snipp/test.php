if ($this->hasAutoIncrementColumn()) {
    $this->registerCallback('before_store after_create', 'cbAutoIncrementColumn');
} elseif (count($this->pk) === 1) {
    $this->registerCallback('before_store', 'cbAutoKeyCreation');
}
