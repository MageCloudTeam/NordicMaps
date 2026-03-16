<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\Api\Data;

interface LabelInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const LABEL_ID = 'label_id';
    public const PROFILE_ID = 'profile_id';
    public const STORE_ID = 'store_id';
    public const ACTION = 'action';
    public const LABEL = 'label';
    /**#@-*/

    /**#@+
     * Action Codes
     */
    public const INIT_MESSAGE = 'init_message';
    public const PROGRESS_MESSAGE = 'progress_message';
    public const ACHIEVED_MESSAGE = 'achieved_message';
    public const TERMS_MESSAGE = 'terms_message';
    /**#@-*/

    /**
     * @return mixed
     */
    public function getLabelId();

    /**
     * @param int $labelId
     *
     * @return LabelInterface
     */
    public function setLabelId($labelId);

    /**
     * @return int
     */
    public function getProfileId();

    /**
     * @param int $profileId
     *
     * @return LabelInterface
     */
    public function setProfileId($profileId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return LabelInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     *
     * @return LabelInterface
     */
    public function setAction($action);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return LabelInterface
     */
    public function setLabel($label);
}
