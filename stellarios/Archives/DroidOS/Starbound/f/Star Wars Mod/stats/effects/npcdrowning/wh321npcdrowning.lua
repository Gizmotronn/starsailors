function init()

  self.minTimeUnderWater = 6.0
  self.timeUnderWater = self.minTimeUnderWater
  self.mouthPosition = 0.0
  self.health = status.resourceMax("health")

  script.setUpdateDelta(3)
  
  self.tickDamagePercentage = 0.2
  self.tickTime = 1.0
  self.tickTimer = self.tickTime
  
  self.musicTickTime = 2.0
  self.musicTickTimer = self.musicTickTime
  
end

function update(dt)

  self.mouthPosition = world.entityMouthPosition(entity.id())
  local liquidAtMouth = world.liquidAt(self.mouthPosition)
  if liquidAtMouth then
	animator.setParticleEmitterActive("bubbles", true)
	self.timeUnderWater = self.timeUnderWater - dt
	self.musicTickTimer = self.musicTickTimer - dt
	
	local actualHealth = status.resourcePercentage("health")
	
	if (self.health * 0.75) > actualHealth and (self.health * 0.4) < actualHealth and self.musicTickTimer <= 0 then
		animator.playSound("bubble1")
		self.musicTickTimer = self.musicTickTime
	elseif (self.health * 0.4) > actualHealth and (self.health * 0.2) < actualHealth and self.musicTickTimer <= 0 then
		animator.stopAllSounds("bubble1")
		animator.playSound("bubble2")
		self.musicTickTimer = self.musicTickTime
	elseif (self.health * 0.2) > actualHealth and self.musicTickTimer <= 0 then
		animator.stopAllSounds("bubble2")
		animator.playSound("bubble3")
		self.musicTickTimer = self.musicTickTime
	end
  else
	animator.setParticleEmitterActive("bubbles", false)
	self.timeUnderWater = self.minTimeUnderWater
  end
  
  self.tickTimer = self.tickTimer - dt
  if self.timeUnderWater <= 0 and self.tickTimer <= 0 then
	self.tickTimer = self.tickTime
	local damage = self.health * 0.05
    status.applySelfDamageRequest({
        damageType = "IgnoresDef",
        damage = damage,
        damageSourceKind = "breath",
        sourceEntityId = entity.id()
      })
	  
  end

end

function uninit()
  
end
