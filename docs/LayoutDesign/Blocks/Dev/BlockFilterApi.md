---
currentMenu: blocks
---
# BlockFilterApi

Interface: `\Zikula\BlocksModule\Api\ApiInterface\BlockFilterApiInterface`.  
Class: `\Zikula\BlocksModule\Api\BlockFilterApi`.

The BlockFilterApi helps determine if blocks should be displayed or not.

The class makes the following methods available:

```php
/**
 * Determine if the block is displayable based on the filter criteria.
 */
public function isDisplayable(BlockEntity $blockEntity): bool;

/**
 * Get all the attributes of the request + 'query param'.
 */
public function getFilterAttributeChoices(): array;
```

The class is fully tested.
