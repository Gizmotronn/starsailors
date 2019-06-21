function init()

  
	self.lastYPosition = 0
	self.lastYVelocity = 0
	self.fallDistance = 0
  
	script.setUpdateDelta(5)
	
  if mcontroller.onGround() then
	effect.expire()
  end

end

function update(dt)

  local minimumFallDistance = 14
  local fallDistanceDamageFactor = 3
  local minimumFallVel = 40
  local baseGravity = 80
  local gravityDiffFactor = 1 / 30.0

  local curYPosition = mcontroller.yPosition()
  local yPosChange = curYPosition - (self.lastYPosition or curYPosition)

  if self.fallDistance > minimumFallDistance and -self.lastYVelocity > minimumFallVel and mcontroller.onGround() then
    local damage = (self.fallDistance - minimumFallDistance) * fallDistanceDamageFactor
    damage = damage * (1.0 + (world.gravity(mcontroller.position()) - baseGravity) * gravityDiffFactor)
    damage = damage * 1.0
    status.applySelfDamageRequest({
        damageType = "IgnoresDef",
        damage = damage,
        damageSourceKind = "falling",
        sourceEntityId = entity.id()
      })
  end

  if mcontroller.yVelocity() < -minimumFallVel and not mcontroller.onGround() then
    self.fallDistance = self.fallDistance + -yPosChange
  else
    self.fallDistance = 0
  end
  
  if mcontroller.onGround() then
	effect.expire()
  end
  
  self.lastYPosition = curYPosition
  self.lastYVelocity = mcontroller.yVelocity()

end

function uninit()
  
end
