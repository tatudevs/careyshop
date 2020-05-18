DROP FUNCTION IF EXISTS `getRegionChildrenList`;
CREATE FUNCTION `getRegionChildrenList`(`rootId` TEXT) RETURNS VARCHAR(4000) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    NO SQL
    COMMENT '根据父ID获取区域所有子级'
BEGIN
DECLARE sTemp VARCHAR(4000);
DECLARE sTempChd VARCHAR(4000);

SET sTemp = NULL;
SET sTempChd = CAST(rootId AS CHAR);

WHILE sTempChd IS NOT NULL DO
IF (sTemp IS NOT NULL) THEN
SET sTemp = CONCAT(sTemp,',',sTempChd);
ELSE
SET sTemp = CONCAT(sTempChd);
END IF;
SELECT GROUP_CONCAT(region_id) INTO sTempChd FROM `{prefix}region` WHERE FIND_IN_SET(parent_id,sTempChd)>0;
END WHILE;
RETURN sTemp;
END;

DROP FUNCTION IF EXISTS `getStorageChildrenList`;
CREATE FUNCTION `getStorageChildrenList`(`rootId` TEXT) RETURNS VARCHAR(4000) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
NO SQL
COMMENT '根据父ID获取资源管理器所有子级'
BEGIN
DECLARE sTemp VARCHAR(4000);
DECLARE sTempChd VARCHAR(4000);

SET sTemp = NULL;
SET sTempChd = CAST(rootId AS CHAR);

WHILE sTempChd IS NOT NULL DO
IF (sTemp IS NOT NULL) THEN
SET sTemp = CONCAT(sTemp,',',sTempChd);
ELSE
SET sTemp = CONCAT(sTempChd);
END IF;
SELECT GROUP_CONCAT(storage_id) INTO sTempChd FROM `{prefix}storage` WHERE FIND_IN_SET(parent_id,sTempChd)>0;
END WHILE;
RETURN sTemp;
END;
