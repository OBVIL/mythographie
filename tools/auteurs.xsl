<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:tei="http://www.tei-c.org/ns/1.0"
  version="1.0">
  <xsl:output method="text" encoding="UTF-8"/>
  <xsl:variable name="tab">
    <xsl:text>&#9;</xsl:text>
  </xsl:variable>
  <xsl:variable name="lf">
    <xsl:text>&#10;</xsl:text>
  </xsl:variable>
  <!-- 
id;lang;key;role;resp
  
  -->
  <xsl:template match="tei:teiHeader"/>
  <xsl:template match="*">
    <xsl:apply-templates select="*"/>
  </xsl:template>
  <xsl:template match="tei:author">
    <xsl:choose>
      <xsl:when test="/*/@xml:id">
        <xsl:value-of select="/*/@xml:id"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="/tei:TEI/tei:teiHeader/tei:fileDesc/tei:titleStmt/tei:author/@key"/>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:value-of select="/*/@xml:id"/>
    <xsl:value-of select="$tab"/>
    <xsl:value-of select="/*/@xml:lang"/>
    <xsl:value-of select="$tab"/>
    <xsl:choose>
      <xsl:when test="@key">
        <xsl:value-of select="@key"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="normalize-space(.)"/>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:value-of select="$tab"/>
    <!--
    <xsl:choose>
      <xsl:when test="@role='translator' and @resp='editor'">
        <xsl:text>translator</xsl:text>
      </xsl:when>
      <xsl:when test="@role='translator'">
        <xsl:text>translator</xsl:text>
      </xsl:when>
    </xsl:choose>
    -->
    <xsl:value-of select="@role"/>
    <xsl:value-of select="$tab"/>
    <xsl:value-of select="@resp"/>
    <xsl:value-of select="$tab"/>
    <xsl:value-of select="name(ancestor::*[self::tei:note or self::tei:quote][1])"/>
    <xsl:value-of select="$lf"/>
  </xsl:template>
</xsl:stylesheet>